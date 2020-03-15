import Vue from "vue";
import Router from "vue-router";

import Login from '../components/Login.vue'
import Home from "../components/Home.vue"
import Welcome from "../components/Home/Welcome"
import Users from "../components/Home/User/Users.vue"
import Rights from "../components/Home/Power/Rights.vue"
import Roles from "../components/Home/Power/Roles.vue"
import Categories from "../components/Home/Goods/Categories.vue"

Vue.use(Router);

const router = new Router({
    routes: [
        // 重定向
        {
            path: '/',
            redirect: '/login'
        },
        {
            path: '/login',
            component: Login
        },
        {
            path: '/home',
            component: Home,
            redirect: "/welcome",
            children: [{
                    path: '/welcome',
                    component: Welcome
                }, {
                    path: '/users',
                    component: Users
                },
                {
                    path: '/rights',
                    component: Rights
                }, {
                    path: '/roles',
                    component: Roles
                }, {
                    path: '/categories',
                    component: Categories
                }
            ]
        }
    ]
})

router.beforeEach((to, from, next) => {
    //    如果用户访问的是登录页直接放行
    if (to.path === "/login") {
        sessionStorage.clear();
        return next();
    }

    const token = sessionStorage.getItem("token");
    if (!token) {
        return next("/login");
    }

    next();

})

export default router;