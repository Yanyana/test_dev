import axios from 'axios';
require('dotenv').config()

const API_URL = process.env.API_URL ? process.env.API_URL : 'http://localhost:8000/api/';

class requestServices {
  list(page) {
    let pageNo = parseInt(page.pageNo);
    return axios.get(API_URL + 'request/?page=' + pageNo + '&per_page=' + page.size);
  }

  create(payload) {
    return axios.post(API_URL + '/request/add', payload);
  }

  // get(id) {
  //   return axios.get(API_URL + '/' + id);
  // }

  // update(payload) {
  //   return axios.put(API_URL + '/', payload);
  // }

  // _delete(id) {
  //   return axios.delete(API_URL + '/' + id);
  // }
}

export default new requestServices();