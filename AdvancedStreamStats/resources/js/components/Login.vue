<template>
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12 col-md-6 offset-md-3">
                <div class="card shadow sm">
                    <div class="card-body">
                        <h1 class="text-center">Login</h1>
                        <hr/>
                        <form action="javascript:void(0)" class="row" method="post">
                            <div class="form-group col-12">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="text" v-model="auth.email" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="password" class="font-weight-bold">Password</label>
                                <input type="password" v-model="auth.password" name="password" id="password" class="form-control">
                            </div>
                            <div class="col-12 mb-2">
                                <button type="submit" :disabled="processing" @click="login" class="btn btn-primary btn-block">
                                    {{ processing ? "Please wait" : "Login" }}
                                </button>
                            </div>
                            <div class="col-12 text-center">
                                <label>Don't have an account? <router-link :to="{name:'register'}">Register Now!</router-link></label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name:"login",
    data(){
        return {
            auth:{
                email:"",
                password:""
            },
            processing:false
        }
    },
    methods:{
        ...mapActions({
            signIn:'auth/login'
        }),
        async login(){
            this.processing = true
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/login',this.auth).then(({data})=>{
                this.signIn()
            }).catch(({response:{data}})=>{
                alert(data.message)
            }).finally(()=>{
                this.processing = false
            })
        },
    }
}
</script>
Create a File inside  resources/js/components  folder name with  Register.vue.

<template>
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12 col-md-6 offset-md-3">
                <div class="card shadow sm">
                    <div class="card-body">
                        <h1 class="text-center">Register</h1>
                        <hr/>
                        <form action="javascript:void(0)" @submit="register" class="row" method="post">
                            <div class="form-group col-12">
                                <label for="name" class="font-weight-bold">Name</label>
                                <input type="text" name="name" v-model="user.name" id="name" placeholder="Enter name" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="text" name="email" v-model="user.email" id="email" placeholder="Enter Email" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="password" class="font-weight-bold">Password</label>
                                <input type="password" name="password" v-model="user.password" id="password" placeholder="Enter Password" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="password_confirmation" class="font-weight-bold">Confirm Password</label>
                                <input type="password_confirmation" name="password_confirmation" v-model="user.password_confirmation" id="password_confirmation" placeholder="Enter Password" class="form-control">
                            </div>
                            <div class="col-12 mb-2">
                                <button type="submit" :disabled="processing" class="btn btn-primary btn-block">
                                    {{ processing ? "Please wait" : "Register" }}
                                </button>
                            </div>
                            <div class="col-12 text-center">
                                <label>Already have an account? <router-link :to="{name:'login'}">Login Now!</router-link></label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export default {
    name:'register',
    data(){
        return {
            user:{
                name:"",
                email:"",
                password:"",
                password_confirmation:""
            },
            processing:false
        }
    },
    methods:{
        ...mapActions({
            signIn:'auth/login'
        }),
        async register(){
            this.processing = true
            await axios.post('/register',this.user).then(response=>{
                this.signIn()
            }).catch(({response:{data}})=>{
                alert(data.message)
            }).finally(()=>{
                this.processing = false
            })
        }
    }
}
</script>
Create Layout Component For All Authenticated Pages. So we don't need to add header, footer, and any other component in all pages component so here we created a layout component named  Dashboard.vue. Here in the component, We add header, footer, and router-view so every component will render in this router-view.

resources/js/components/layouts/Dashboard.vue

<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a href="https://techvblogs.com/?ref=project" target="_blank" class="navbar-brand">TechvBlogs</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <router-link :to="{name:'dashboard'}" class="nav-link">Home <span class="sr-only">(current)</span></router-link>
                    </li>
                </ul>
                <div class="ml-auto">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ user.name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="javascript:void(0)" @click="logout">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="mt-3">
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
import {mapActions} from 'vuex'
export default {
    name:"dashboard-layout",
    data(){
        return {
            user:this.$store.state.auth.user
        }
    },
    methods:{
        ...mapActions({
            signOut:"auth/logout"
        }),
        async logout(){
            await axios.post('/logout').then(({data})=>{
                this.signOut()
                this.$router.push({name:"login"})
            })
        }
    }
}
</script>
