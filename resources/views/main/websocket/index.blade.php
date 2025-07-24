@extends('master', [
    'use_tailwind' => true,
])
@section('style')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.4.47/css/materialdesignicons.min.css"
        integrity="sha512-/k658G6UsCvbkGRB3vPXpsPHgWeduJwiWGPCGS14IQw3xpr63AEMdA8nMYG2gmYkXitQxDTn6iiK/2fD4T87qA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/material-darker.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
    <div id="app">
        <main class="nxl-container">
            <div class="nxl-content">
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">Websocket</li>
                            <li class="breadcrumb-item">Development Mode</li>
                        </ul>
                    </div>
                </div>
                <div class="main-content">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <h2 class="text-lg font-medium">Tutorial</h2>
                                <textarea readonly id="code-editor" v-model="code" class="mt-2 p-2 border border-gray-300 rounded-md w-full h-64"></textarea>
                            </div>
                            <form @submit.prevent="subscribe" class="mt-6">
                                <div class="mb-4">
                                    <label for="channel" class="block text-sm font-medium text-gray-700">Channel</label>
                                    <input type="text" id="channel" v-model="channel"
                                        class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
                                </div>
                                <div class="mb-4">
                                    <label for="event" class="block text-sm font-medium text-gray-700">Event</label>
                                    <input type="text" id="event" v-model="event"
                                        class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
                                </div>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-md mr-2">Subscribe</button>
                                <button type="button" @click="unsubscribe"
                                    class="px-4 py-2 bg-red-500 text-white rounded-md mr-2">Unsubscribe</button>
                                <div class="my-4">
                                    <label for="event" class="block text-sm font-medium text-gray-700">Endpoint Testing
                                        Send Event</label>
                                    <div class="row">

                                        <div class="col-auto">
                                            <select v-model="eventTestMethod"
                                                class="mt-1 p-2 border border-gray-300 rounded-md w-[100px]">
                                                <option value="">Method</option>
                                                <option value="GET">GET</option>
                                                <option value="POST">POST</option>
                                                <option value="PUT">PUT</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="text" id="event" v-model="eventTestUrl"
                                                class="mt-1 p-2 border border-gray-300 rounded-md w-full" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" @click="testsendevent"
                                    class="px-4 py-2 bg-orange-500 text-white rounded-md">Test Send Event</button>
                            </form>

                            <div class="mt-6">
                                <h2 class="text-lg font-medium">Log</h2>
                                <div class="flex flex-col gap-[0px] mt-2 h-[300px] border overflow-y-auto">
                                    <div v-for="item in logs">
                                        @{{ item }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('js')
    {{-- <script src="https://unpkg.com/vue@3.2.47/dist/vue.global.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/material-darker.min.js"></script>
    <script>
        // const {
        //     createApp,
        //     ref,
        //     reactive,
        //     onMounted
        // } = Vue;

        Pusher.logToConsole = true;

        var pusherx = new Pusher('qwerty', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            wsHost: '{{ env('PUSHER_FE_HOST') }}',
            wsPort: {{ env('PUSHER_PORT') }},
            wssPort: 443,
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            encrypted: true
        });

        const appUrl = "{{ env('APP_URL') }}"
        createApp({
            setup() {
                const channel = ref('');
                const event = ref('');
                const eventTestUrl = ref(appUrl + '/api/websocket/test-send-message')
                const eventTestMethod = ref('GET')
                const logs = reactive([]);
                let currentChannel = ref();
                const code = ref(`Pusher.logToConsole = true;

var pusher = new Pusher('qwerty', {
    cluster: 'mt1',
    wsHost: 'dev.randu.co.id',
    wsPort: 6001,
    wssPort: 443,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    encrypted: true
});

// Berlangganan ke channel
var channel = pusher.subscribe('my-channel');

// Mendengarkan event
channel.bind('e1', function(data) {
    console.log('Event received:', data);
});

// Logging tambahan untuk debugging
pusher.connection.bind('connected', function() {
    console.log('Successfully connected to Pusher');
});

channel.bind('pusher:subscription_succeeded', function() {
    console.log('Subscribed to my-channel');
});

channel.bind('pusher:subscription_error', function(status) {
    console.error('Subscription error:', status);
});`);

                onMounted(() => {
                    const codeEditor = CodeMirror.fromTextArea(document.getElementById("code-editor"), {
                        lineNumbers: true,
                        mode: "javascript",
                        theme: "material-darker",
                        readOnly: true
                    });

                    codeEditor.on("change", () => {
                        code.value = codeEditor.getValue();
                    });

                    // Logging tambahan untuk debugging
                    pusherx.connection.bind('connected', () => {
                        console.log('Successfully connected to Pusher');
                        logs.push('Successfully connected to Pusher');
                    });
                });

                const subscribe = () => {
                    if (!currentChannel.value) {
                        currentChannel.value = pusherx.subscribe(channel.value);

                        currentChannel.value.bind(event.value, (data) => {
                            console.log(data);
                            logs.push(JSON.stringify(data, null, 2));
                        });

                        currentChannel.value.bind('pusher:subscription_succeeded', () => {
                            console.log(`Subscribed to ${channel.value}`);
                            logs.push(`Subscribed to ${channel.value}`);
                        });

                        currentChannel.value.bind('pusher:subscription_error', (status) => {
                            console.error('Subscription error:', status);
                            logs.push(`Subscription error: ${status}`);
                        });
                    }

                };

                const unsubscribe = () => {
                    if (currentChannel.value) {
                        pusherx.unsubscribe(channel.value);
                        console.log(`Unsubscribed from ${channel.value}`);
                        logs.push(`Unsubscribed from ${channel.value}`);
                        currentChannel.value = null;
                    }
                };

                const testsendevent = () => {
                    axios({
                        method: eventTestMethod.value,
                        url: eventTestUrl.value
                    }).then((res) => {
                        logs.push(res.data)
                    })
                }

                return {
                    testsendevent,
                    eventTestMethod,
                    channel,
                    event,
                    eventTestUrl,
                    logs,
                    subscribe,
                    unsubscribe,
                    code,
                };
            }
        }).mount('#app');
    </script>
@endsection
