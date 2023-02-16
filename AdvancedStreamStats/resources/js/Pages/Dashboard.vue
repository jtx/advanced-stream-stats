<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    card_number: '',
    expiration_date: 'mm/yy',
    cvv: '',
});

const props = defineProps( {
    subscriptions: Array,
    plans: Array,
    hasYearly: Boolean,
});


</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12" v-if="subscriptions.length">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Your Subscriptions</h2>

                    <ul>
                        <li v-for="subscription in subscriptions">
                            {{ subscription.planId }} - ${{ subscription.price }}<br />
                            Next billing date: {{ subscription.nextBillingDate }} -> Cancel
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Purchase New Subscription</h2>

                    <form action="javascript:void(0)" class="row" method="post">
                        <div class="form-group col-12">
                            <label for="subscription" class="font-weight-bold">Subscription Plan: </label>
                            <select id="subscription" name="subscription" class="form-control">
                                <option v-for="plan in plans" :value="plan.id">
                                    {{ plan.description }} - ${{ plan.price }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-12">
                                <label for="card_number" class="font-weight-bold">Card Number: </label>
                                <input type="text" v-model="form.card_number" name="card_number" id="card_number" class="form-control">
                        </div>
                        <div class="form-group col-12">
                            <label for="expiration_date" class="font-weight-bold">Expiration Date: </label>
                            <input type="text" v-model="form.expiration_date" name="expiration_date" id="expiration_date" class="form-control">
                        </div>
                        <div class="form-group col-12">
                            <label for="cvv" class="font-weight-bold">Expiration Date: </label>
                            <input type="text" v-model="form.cvv" name="cvv" id="cvv" class="cvv">
                        </div>

                        <div class="col-12 mb-2">
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Purchase
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
