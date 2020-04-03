<template>
  <div class="container">
    <b-form @submit="onSubmit" v-if="show">
      <b-form-group id="input-group-2" label="Full Name:" label-for="input-2">
        <b-form-input id="input-2" v-model="form.name" required placeholder="Enter name"></b-form-input>
      </b-form-group>
      <b-form-group id="input-group-2" label="Balance:" label-for="input-2">
        <b-form-input id="input-2" v-model="form.balance" required placeholder="Enter starting balance" type="number"></b-form-input>
      </b-form-group>

      <b-button nuxt-link :to="'/'" variant="primary">&laquo;</b-button>
      <b-button type="submit" variant="primary">Create Account</b-button>
    </b-form>
  </div>
</template>

<script lang="ts">
import axios from "axios";
import Vue from "vue";

export default {
  data() {
    return {
      form: {
        name: '',
        balance: ''
      },
      show: true
    }
  },
  methods: {
    onSubmit(evt) {
      evt.preventDefault()

      let data = {
        name: this.form.name,
        balance: this.form.balance
      };

      axios.post(process.env.API_SERVER + '/api/accounts', data)
        .then(response => {
          window.location = '/accounts/' + response.data.id
        })
    },
  }
};
</script>

<style scoped>
.container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}
</style>
