<template>
  <div>
    <div class="container" v-if="loadingAccount">loading...</div>

    <div class="container" v-if="!loadingAccount">
      <b-card :header="'Welcome, ' + account.name" class="mt-3">
        <b-card-text>
          <div>
            Account: <code>{{ account.id }}</code>
          </div>
          <div>
            Balance:
            <code>{{ account.currency.symbol }}{{ account.balance }}</code>
          </div>
        </b-card-text>
        <b-button size="sm" variant="success" @click="show = !show"
          >New payment</b-button
        >

        <b-button
          class="float-right"
          variant="danger"
          size="sm"
          nuxt-link
          to="/"
          >Logout</b-button
        >
      </b-card>

      <b-card class="mt-3" header="New Payment" v-show="show">
        <b-form @submit="onSubmit">
          <b-form-group id="input-group-1" label="To:" label-for="input-1">
            <b-form-input
              id="input-1"
              size="sm"
              v-model="payment.to"
              type="number"
              required
              placeholder="Destination ID"
            ></b-form-input>
          </b-form-group>

          <b-form-group id="input-group-2" label="Amount:" label-for="input-2">
            <b-input-group prepend="$" size="sm">
              <b-form-input
                id="input-2"
                v-model="payment.amount"
                type="number"
                required
                placeholder="Amount"
              ></b-form-input>
            </b-input-group>
          </b-form-group>

          <b-form-group id="input-group-3" label="Details:" label-for="input-3">
            <b-form-input
              id="input-3"
              size="sm"
              v-model="payment.details"
              required
              placeholder="Payment details"
            ></b-form-input>
          </b-form-group>

          <b-button type="submit" size="sm" variant="primary">Submit</b-button>
        </b-form>
      </b-card>

      <b-alert v-model="hasErrorMessage" variant="danger" dismissible class="mt-3">
        {{ errorMessage }}
      </b-alert>

      <b-card class="mt-3" header="Payment History">
        <div v-if="loadingTransactions">loading transactions...</div>
        <b-table v-if="!loadingTransactions && transactions.length>0" striped hover :items="transactions"></b-table>
        <div class="mx-auto font-italic w-25" v-if="!loadingTransactions && transactions.length==0">No transaction history</div>
      </b-card>
    </div>
  </div>
</template>

<script lang="ts">
import axios from "axios";
import Vue from "vue";

export default {
  data() {
    return {
      show: false,
      payment: {},

      account: null,
      transactions: [],

      hasErrorMessage: false,
      errorMessage: true,

      loadingAccount: true,
      loadingTransactions: true
    };
  },

  mounted() {
    const that = this;

    axios
      .get(process.env.API_SERVER + `/api/accounts/${this.$route.params.id}`)
      .then(function(response) {
        that.account = response.data;

        if (that.account) {
          that.loadingAccount = false;
        }
      })
      .catch(function(error) {
        window.location = "/";
      })
      .then(function() {
        that.getTransactions()
      });
  },

  methods: {
    createTransactionRow(transaction) {
      let row = [];
      row['Account'] = transaction.sender.name + "#" + transaction.sender.id;
      row['Details'] = transaction.details;

      row['Amount'] = this.account.currency.symbol + transaction.amount;

      if (this.account.id != transaction.to) {
        row['Account'] = transaction.recipient.name + "#" + transaction.recipient.id;
        row['Amount'] = "(-" + row['Amount'] + ")";
      }

      return row;
    },
    getTransactions() {
      var that = this;

      axios
        .get(process.env.API_SERVER + `/api/accounts/${that.$route.params.id}/transactions`)
        .then(function(response) {
          let transactions = response.data;

          for (let i = 0; i < transactions.length; i++) {
            let row = that.createTransactionRow(transactions[i]);
            that.transactions.push(row);
          }

          if (that.account && that.transactions) {
            that.loadingTransactions = false;
          }
        });
    },
    onSubmit(evt) {
      var that = this;
      this.hasErrorMessage = false;

      evt.preventDefault();

      axios
        .post(process.env.API_SERVER + `/api/accounts/${this.$route.params.id}/transactions`, this.payment)
        .then(function(response) {
          let transaction = that.createTransactionRow(response.data);
          that.transactions.push(transaction);
          that.account.balance = parseInt(that.account.balance) - parseInt(that.payment.amount)
        })
        .catch(function(error) {
          that.hasErrorMessage = true;
          that.errorMessage = error.response.data.error;
        })
        .then(() => {
          that.payment = {};
          that.show = false;
        });

    }
  }
};
</script>
