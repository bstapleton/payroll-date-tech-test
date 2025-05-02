<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const showForm = ref(true);
const responseData = ref(usePage().props.response);

const toggleForm = () => {
    showForm.value = !showForm.value;
}

const currentDate = new Date();
const form = useForm({
    year: currentDate.getFullYear(),
    month: currentDate.getMonth(),
});

const submit = () => {
    form.post(route('show'), {
        onFinish: () => {
            form.reset('year', 'month');
            responseData.value = usePage().props.response;
            toggleForm();
        },
    });
};
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <div class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:px-20 lg:py-14"
                >
                    <h1 class="mb-8 text-xl">Check a payday</h1>

                    <form v-if="showForm" @submit.prevent="submit">
                        <div class="mb-4">
                            <Label for="year">Year</Label>
                            <Input tabindex="1" id="year" type="number" name="year" autocomplete="year" v-model="form.year" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.year" class="mt-2" />
                        </div>

                        <div>
                            <Label for="month">Month</Label>
                            <Input tabindex="2" id="month" type="number" name="month" autocomplete="month" v-model="form.month" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.month" class="mt-2" />
                        </div>

                        <Button tabindex="3" type="submit" class="mt-4 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Submit
                        </Button>
                    </form>
                    <div v-else-if="responseData">
                        <div class="mb-4">
                            <ul class="list-none">
                                <!-- I've not really used inertia before with Vue, and in previous similar tasks I've used Axios responses. I don't really like how messy the dot-notation is here, but it works. -->
                                <li class="mb-2">
                                    Transfer date: {{ responseData.data.original.data.transfer_date }}<br />
                                    <span class="italic text-gray-400">This is the latest date to initiate a payment transfer to ensure that it arrives on or before the payday date listed below.</span>
                                </li>
                                <li class="mb-2">
                                    Payday: {{ responseData.data.original.data.payday }}<br />
                                    <span class="italic text-gray-400">This is the latest date that the employees will receive their pay based on the payment date listed above.</span>
                                </li>
                            </ul>
                            <a href="/" class="underline">Try another date</a>
                        </div>
                    </div>
                    <div v-else>
                        <p>Something went wrong...</p>
                        <a href="/" class="underline">Try another date</a>
                    </div>
                </div>
            </main>
        </div>
        <div class="h-14.5 hidden lg:block"></div>
    </div>
</template>
