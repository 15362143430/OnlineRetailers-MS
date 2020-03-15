import axios from 'axios';
axios.defaults.baseURL = 'http://api.vue.com:83/';
// Vue.prototype.$axios = axios;
// console.log(666)

import qs from 'qs';

// import $ from 'jquery';
import vue from "../main";
axios.interceptors.response.use(
    response => {
        //拦截响应，做统一处理 
        // console.log(response)
        if (response.data.meta.status === 401) {
            vue.$router.push("/login");
            return vue.$message.error(response.data.meta.msg);
        }
        if (response.data.meta.status === 4001) {
            return vue.$message.error(response.data.meta.msg);
        }
        return response
    }

)

export function a(fn) {
    console.log((/^[\s(]*function(?:\s+([\w$_][\w\d$_]*))?\(/).exec(fn.toString())[1]);
}

export function Login(data = {}) {
    return axios.post("user/login", qs.stringify(data));
}

export function Register(data = {}) {
    return axios.post("user/register", qs.stringify(data));
}

export function httpGet(url,method_name, params={}) {
    params.username = sessionStorage.getItem("username");
    params.token = sessionStorage.getItem("token");
    params.roleID = sessionStorage.getItem("role_id");
    params.method_name = method_name;
    return axios.get(url, {params});
}

export function httpPost(url,method_name, data = {}) {
    data.username = sessionStorage.getItem("username");
    data.token = sessionStorage.getItem("token");
    data.roleID = sessionStorage.getItem("role_id");
    data.method_name = method_name;
    return axios.post(url, qs.stringify(data));
}

export function httpDelete(that, url,method_name, data = {}) {
    return new Promise((resolve) => {
        that.$confirm('此操作将永久删除, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        }).then(() => {
            data.username = sessionStorage.getItem("username");
            data.token = sessionStorage.getItem("token");
            data.roleID = sessionStorage.getItem("role_id");
            data.method_name = method_name;
            resolve(
                axios.post(url, qs.stringify(data))
            )
        }).catch(() => {
            that.$message({
                type: 'info',
                message: '已取消删除'
            });
        });
    })

}

export function httpPut(url,method_name, data = {}) {
    data.username = sessionStorage.getItem("username");
    data.token = sessionStorage.getItem("token");
    data.roleID = sessionStorage.getItem("role_id");
    data.method_name = method_name;
    return axios.put(url, qs.stringify(data));
}

export function getList(url,method_name, data = {
    pageNum: 1,
    pageSize: 5
}) {
    data.username = sessionStorage.getItem("username");
    data.token = sessionStorage.getItem("token");
    data.roleID = sessionStorage.getItem("role_id");
    data.method_name = method_name;
    return axios.post(url, qs.stringify(data));
}

export function getListbyGET(url,method_name, params = {
    pageNum: 1,
    pageSize: 5
}) {
    params.username = sessionStorage.getItem("username");
    params.token = sessionStorage.getItem("token");
    params.roleID = sessionStorage.getItem("role_id");
    params.method_name = method_name;
    return axios.get(url, {params});
}

export function getListByQuery(url, query,method_name, data = {
    pageNum: 1,
    pageSize: 5
}) {
    if (query !== "") {
        data.query = query;
    }
    data.username = sessionStorage.getItem("username");
    data.token = sessionStorage.getItem("token");
    data.roleID = sessionStorage.getItem("role_id");
    data.method_name = method_name;
    return axios.post(url, qs.stringify(data));
}