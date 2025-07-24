<!-- CSS for Lightbox -->
<link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet" />

<!-- JS for Lightbox -->
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
<style>
    .btn.btn-intercom {
        background-color: rgb(12, 75, 168, 0.9);
        color: white;
    }

    .btn.btn-intercom.btn-intercom-init {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        position: fixed;
        right: 100px;
        bottom: 20px;
        padding: 3px;
        box-sizing: border-box;
        -webkit-box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0.6);
        -moz-box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0.6);
        box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0.6);
    }

    .btn.btn-intercom.btn-intercom-init .btn-intercom-icon {
        width: 42px;
        height: 42px;
        position: relative;
        margin: auto;
        top: 0px;
        background-image: url({{ $dataConfig->randuai_button_icon }});
        background-size: contain;
        background-repeat: no-repeat;
    }

    .btn-intercom-open {
        animation: btnIntercomOpen 0.4s ease 1 normal forwards;
    }

    .btn-intercom-close {
        animation: btnIntercomClose 0.4s ease 1 normal forwards;
    }

    @keyframes btnIntercomOpen {
        25% {
            background-image: url({{ $dataConfig->randuai_button_icon }});
        }

        100% {
            width: 20px;
            height: 20px;
            top: 0px;
            transform: rotate(180deg);
            background-image: url(/vendor/intercom/img/icon/icon-close.png);
        }
    }

    @keyframes btnIntercomClose {
        0% {
            width: 20px;
            height: 20px;
            top: 0px;
            background-image: url(/vendor/intercom/img/icon/icon-close.png);
            transform: rotate(180deg);
        }

        5% {
            background-image: url(/vendor/intercom/img/icon/icon-close.png);
        }

        80% {
            background-image: url({{ $dataConfig->randuai_button_icon }});
        }

        100% {
            background-image: url({{ $dataConfig->randuai_button_icon }});
        }
    }

    .popup-intercom {
        position: fixed;
        width: 100%;
        height: 100%;
        bottom: -50px;
        opacity: 0;
        transition: all 0.4s ease;
        display: none;
        z-index: 10000;
    }

    .popup-intercom-open {
        bottom: 0px;
        opacity: 1;
    }

    .popup-intercom-layout {
        position: relative;
        width: 100%;
        height: 100%;
        background-color: #eee;
    }

    .popup-intercom-layout .popup-intercom-box {
        position: relative;
        width: 100%;
        height: 100%;
        overflow-y: auto;
        z-index: 1;
        padding-bottom: 110px;
    }

    .popup-intercom-header {
        position: relative;
        width: 100%;
        height: var(--dynamic-height, 250px);
        padding: 25px 30px 0px 35px;
        z-index: 2;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .popup-intercom-header::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: var(--dynamic-height, 250px);
        /* background-color: blue; */
        background-image: url(/vendor/intercom/img/background/intercom.png),
            linear-gradient(to left,
                rgba(64, 125, 214, 0.8) 5%,
                rgb(12, 75, 168, 0.9) 80%);
        border-top-left-radius: inherit;
        border-top-right-radius: inherit;
        z-index: 1;
    }

    .popup-intercom-header .intercom-logo {
        position: relative;
        z-index: 3;
    }

    .popup-intercom-header .popup-intergom-logo {
        margin-left: -4px;
        height: 45px;
    }

    .popup-intercom-header .popup-intercom-title {
        position: relative;
        margin-top: 15px;
        font-size: 25px;
        color: white;
        z-index: 3;
    }

    .popup-intercom-header .popup-intercom-subtitle {
        position: relative;
        margin-top: 5px;
        font-size: 14px;
        color: #e7e7e7;
        font-weight: 300;
        z-index: 3;
    }

    .popup-intercom-body {
        position: absolute;
        width: 100%;
        height: calc(100% - var(--dynamic-height));
        background: #eee;
        z-index: 2;
        padding: 0px 15px 0px 15px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    /*.popup-intercom-body .popup-intercom-box-chat {
        position: absolute;
        bottom: 15px;
        width: calc(100% - 30px);
        height: 60px;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 5px;
    }*/
    .popup-intercom-body {
        position: absolute;
        width: 100%;
        height: calc(100% - var(--dynamic-height, 250px));
        background: #eee;
        z-index: 2;
        padding: 8px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        /* Membuat chat berada di bawah */
    }

    .popup-intercom-box-chat {
        flex-grow: 1;
        overflow-y: auto;
        padding: 10px;
        background-color: #fff;
        color: white;
        border-radius: 10px;
        color: #333;
        /* Warna teks default */
    }

    .chat-message {
        margin: 0;
        padding: 4px;
        border-radius: 10px;
        max-width: 100%;
        word-wrap: break-word;
    }

    /* Styling untuk pesan user */
    .chat-message.you {
        background-color: #fff;
        /* Background putih untuk pesan user */
        color: #333;
        /* Warna teks hitam */
        align-self: flex-end;
        text-align: right;
    }

    /* Styling untuk balasan ChatGPT */
    .chat-message.chatgpt {
        background-color: #fff;
        /* Background abu-abu untuk ChatGPT */
        color: #333;
        /* Warna teks hitam */
        align-self: flex-start;
        text-align: left;
        margin-top: 5px
    }

    .popup-intercom-chat-input {
        background-color: #fff;
        border-radius: 20px;
        padding: 10px;
        margin-top: 10px;
    }

    .popup-intercom-chat-input-child {
        display: flex;
        align-items: center;
    }

    .attachment-menu {
        margin-right: 0px;
        /* Memberi jarak antara ikon attachment dan input teks */
    }

    .attachment-btn {
        background-color: transparent;
        border: none;
        color: #333;
        font-size: 18px;
        cursor: pointer;
        outline: none;
    }

    .popup-intercom-chat-input-child input {
        flex-grow: 1;
        border: none;
        background-color: transparent;
        color: #333;
        padding: 10px;
        outline: none;
    }

    .popup-intercom-chat-input-child textarea {
        flex-grow: 1;
        border: none;
        background-color: transparent;
        color: #333;
        padding: 10px;
        outline: none;
        resize: none;
        /* Nonaktifkan resize manual */
        max-height: 250px;
        /* Tinggi maksimum */
        overflow-y: hidden;
        /* Scroll jika tinggi melebihi batas */
        font-size: 14px;
        line-height: 1.5;
        border-radius: 10px;
        height: 30px;
        /* Tinggi awal lebih kecil */
        min-height: 30px;
        /* Menjaga tinggi minimal untuk satu baris */
    }

    .popup-intercom-chat-input-child input::placeholder {
        color: #999;
    }

    .popup-intercom-chat-input-child button {
        background-color: transparent;
        border: none;
        color: #333;
        font-size: 18px;
        cursor: pointer;
        outline: none;
        padding-left: 10px;
    }


    /* Bootstrap+ */
    .avatar-groups {
        margin-left: 8px;
    }

    .avatar-groups .avatar {
        float: left;
        margin-left: -10px;
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background-color: red;
        object-fit: cover;
        border: 3px solid white;
        box-sizing: border-box;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 7px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #cfcfcf;
        border: 0px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888;
        border: 0px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #666;
    }

    #btn-close-intercom {
        display: block;
    }

    @media (min-width: 992px) {
        #btn-close-intercom {
            display: none;
        }

        .btn.btn-intercom.btn-intercom-init {
            -webkit-box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0);
            -moz-box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0);
            box-shadow: -2px 2px 10px 0px rgba(0, 0, 0, 0);
        }

        .popup-intercom-layout .popup-intercom-box {
            padding-bottom: 30px;
        }

        /* Popup Intercom */
        .popup-intercom {
            width: 420px;
            height: 80%;
            right: 35px;
            bottom: 80px;
        }

        .popup-intercom-open {
            bottom: 100px;
            opacity: 1;
        }

        .popup-intercom-layout {
            border-radius: 10px;
            background-color: #fff;
            -webkit-box-shadow: 0px 5px 40px 0px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0px 5px 40px 0px rgba(0, 0, 0, 0.2);
            box-shadow: 0px 5px 40px 0px rgba(0, 0, 0, 0.2);
        }

        /* Scrollbar */
        /* Track */
        ::-webkit-scrollbar-track {
            background: #cfcfcf;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #666;
        }
    }

    .file-input {
        display: none;
    }

    .preview-thumbnail {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        margin-right: 10px;
        object-fit: cover;
        cursor: pointer;
    }

    .btn-link {
        background: none;
        border: none;
        cursor: pointer;
    }

    .position-absolute {
        right: 8px;
        z-index: 1;
    }

    .uploaded-image {
        max-width: 350px
    }
</style>

<div id="app-intercom">
    <div class="popup-intercom">
        <div ref="popupIntercomLayout" class="popup-intercom-layout">
            <div class="popup-intercom-box">
                <div class="popup-intercom-header">
                    <div class="row intercom-logo">
                        <div class="col text-white"
                            style="font-size: 24px; font-weight: 700; display: flex; align-items: center">
                            <img src="{{ $dataConfig->randuai_header_icon }}" class="popup-intergom-logo" />
                            <span style="margin-left: 10px">Randu AI</span>
                        </div>
                        <div class="col-auto my-auto my-auto text-white me-3" style="font-size: 25px; cursor: pointer">
                            <div v-if="data.conversation_mode !== 'favorites'" @click="methods.onShowingConversationFav"
                                class="fa-solid fa-star"></div>
                            <div v-else @click="methods.onClosingConversation" class="fa-solid fa-times-circle">
                            </div>
                        </div>
                        <div class="col-auto my-auto my-auto text-white me-3" style="font-size: 25px; cursor: pointer">
                            <div v-if="data.conversation_mode !== 'histories'" @click="methods.onShowingConversation"
                                class="fa-solid fa-rotate-left"></div>
                            <div v-else @click="methods.onClosingConversation" class="fa-solid fa-times-circle"></div>
                        </div>
                        <div class="col-auto my-auto my-auto text-white" style="font-size: 25px; cursor: pointer">
                            <div @click="methods.onCreateNewConversation" class="fa-solid fa-pen-to-square"></div>
                        </div>
                        <div class="col-auto d-flex d-md-none justify-content-end my-auto text-white"
                            style="font-size: 25px; cursor: pointer; margin-left: 20px">
                            <div @click="methods.onCloseIntercom" id="btn-close-intercom" class="fas fa-times-circle">
                            </div>
                        </div>
                    </div>
                    <div v-if="!data.hasIntro" class="popup-intercom-title">
                        Hai, Nama Saya Istabel 👋
                    </div>
                    <div v-if="!data.hasIntro" class="popup-intercom-subtitle">
                        {!! $dataConfig->randuai_intro !!}
                    </div>
                </div>
                <div v-if="['default', 'favorites'].includes(data.conversation_mode)" class="popup-intercom-body">
                    <div v-if="data.conversation_mode === 'favorites'" class="popup-intercom-box-chat">
                        <div class="row col-12">
                            <select id="axy" class="form-control form-control-sm mb-3"
                                @change="methods.onSelectFavorite">
                                <option v-for="item in data.conversation_fav_ctgs" :value="item">
                                    @{{ item }}
                                </option>
                            </select>
                            <div class="col-12">
                                <div v-if="data.conversation_list_is_progress"
                                    class="d-flex justify-content-center align-items-center mt-3"
                                    style="font-weight: bold; font-size: 14px">
                                    <span class="fas fa-spin fa-spinner"></span> <span
                                        style="margin-left: 5px">Loading...</span>
                                </div>
                            </div>
                            <div @click="methods.onSelectFavConversation(item.question)"
                                class="col-auto px-2 py-1 border me-2"
                                style="background-color: #eee; border-radius: 5px; cursor: pointer"
                                v-for="item in data.conversation_favs">
                                @{{ item.question.length > 50 ? item.question.substring(0, 50) + '...' : item.question }}
                            </div>
                        </div>
                    </div>
                    <div v-else class="popup-intercom-box-chat" ref="chatBox">
                        <div v-for="message in data.messages" :key="message.id"
                            :class="['chat-message', message.sender === 'You' ? 'you' : 'chatgpt']">
                            <template v-if="message.sender === 'You'">
                                <small v-html="moment(message.created_at).format('DD-MM-YYYY HH:mm')"
                                    style="margin-right: 5px"></small>
                                <strong><span v-html="message.sender"></span></strong>
                            </template>
                            <template v-else>
                                <strong><span v-html="message.sender" style="margin-right: 5px"></span></strong>
                                <small v-html="moment(message.created_at).format('DD-MM-YYYY HH:mm')"></small>
                            </template>
                            <div v-html="message.content"></div> <!-- Tampilkan konten HTML -->
                        </div>
                    </div>

                    <form @submit.prevent="methods.sendMessage" class="popup-intercom-chat-input">
                        <!-- Preview gambar sebelum dikirim -->
                        <div class="row px-4">
                            <div v-for="(image, index) in data.previewImages" :key="index"
                                class="col-auto position-relative">
                                <!-- Gunakan anchor tag untuk Lightbox -->
                                <a :href="image.src" data-lightbox="image-preview" data-title="Preview Image">
                                    <img :src="image.src" alt="Preview Image" class="preview-thumbnail">
                                </a>
                                <div @click="methods.removeImage(index)" class="position-absolute top-0">
                                    <i class="fas fa-times-circle text-danger"
                                        style="cursor: pointer; font-size: 20px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="popup-intercom-chat-input-child">
                            <!-- Tambahkan tombol attachment di sini -->
                            <div class="attachment-menu">
                                <button class="attachment-btn" @click="methods.triggerFileUpload">
                                    <i class="fas fa-paperclip"></i> <!-- Ikon attachment -->
                                </button>
                                <input type="file" id="upload-file" class="file-input"
                                    @change="methods.handleFileChange" ref="fileInput" multiple />
                            </div>

                            {{-- <input type="text" placeholder="Kirim pesan ke ChatGPT" /> --}}
                            <textarea @keydown.enter.prevent="methods.sendMessage" v-model="data.message" @input="methods.adjustTextareaHeight"
                                placeholder="Ketik pesan..." ref="chatTextarea"></textarea>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i> <!-- Ikon kirim pesan -->
                            </button>
                        </div>
                    </form>
                </div>
                <div v-else-if="data.conversation_mode === 'histories'" class="popup-intercom-body">
                    <div class="popup-intercom-box-chat">
                        <div v-if="data.conversation_list_is_progress"
                            class="d-flex justify-content-center align-items-center mt-3"
                            style="font-weight: bold; font-size: 14px">
                            <span class="fas fa-spin fa-spinner"></span> <span
                                style="margin-left: 5px">Loading...</span>
                        </div>
                        <div>
                            <div @click="methods.onClickDetailConversation(conversation.conversation_key)"
                                :style="{
                                    borderTop: index ? '1px solid #ccc' : '',
                                    backgroundColor: conversation.conversation_key === data.conversation_key ? '#eee' :
                                        'transparent'
                                }"
                                style="padding: 5px; cursor: pointer"
                                v-for="(conversation, index) in data.conversation_list" :key="conversation.id">
                                <div>
                                    <strong>@{{ conversation.title }}</strong>
                                </div>
                                <div>
                                    <small>@{{ moment(conversation.created_at).format('DD-MM-YYYY HH:mm') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div @click="methods.onTogglePopup" class="btn rounded-circle btn-intercom btn-intercom-init">
        <div class="btn-intercom-icon"></div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    const {
        createApp,
        ref,
        reactive,
        onMounted,
        watch,
        nextTick
    } = Vue;
    let intercomHasOpened = false;

    createApp({
        setup() {
            const data = reactive({
                hasIntro: false,
                message: '', // Menyimpan pesan yang diketik
                messages: [], // Menyimpan semua pesan dalam chat
                selectedFile: null, // Untuk menyimpan file yang dipilih
                previewImages: [], // Menyimpan gambar yang dipilih untuk di-preview
                conversation_key: null, //
                conversation_mode: 'default',
                conversation_list: [], // Menyimpan
                conversation_list_is_progress: false,
                conversation_fav_mode: false,
                conversation_favs: [], // Menyimpan
                conversation_fav_ctgs: [], // Menyimpan
            })
            const fileInput = ref(null);
            const chatTextarea = ref(null);
            const chatBox = ref(null);
            const popupIntercomLayout = ref(null);
            const methods = {
                onPaste: (event) => {
                    event.preventDefault(); // Mencegah default behavior paste
                    const items = event.clipboardData.items;
                    let isImagePasted = false;

                    // Loop through the clipboard items
                    for (const item of items) {
                        if (item.type.indexOf('image') !== -1) {
                            // Handle image paste
                            const blob = item.getAsFile();
                            const reader = new FileReader();

                            reader.onload = (e) => {
                                // Tambahkan gambar ke thumbnail preview
                                data.previewImages.push({
                                    src: e.target.result,
                                    file: blob,
                                });
                            };
                            reader.readAsDataURL(blob);
                            isImagePasted = true;
                        }
                    }

                    if (!isImagePasted) {
                        // Jika tidak ada gambar, set nilai message langsung
                        const text = event.clipboardData.getData('text/plain');
                        data.message += text; // Tambahkan teks ke data.message
                    }

                    // Mencegah event default jika gambar dipaste
                    if (isImagePasted) {
                        event.preventDefault();
                    }
                },
                onCloseIntercom: () => {
                    intercomHasOpened = false;
                    $(".btn-intercom-icon").removeClass("btn-intercom-open");
                    $(".btn-intercom-icon").addClass("btn-intercom-close");
                    $(".popup-intercom").removeClass("popup-intercom-open");
                    setTimeout(() => {
                        if ($(".popup-intercom").css('display') === 'block') {
                            $(".popup-intercom").css('display', 'none');
                        }
                        if (!data.hasIntro) {
                            localStorage.setItem('intercom-init', true)
                            data.hasIntro = true
                        }
                    }, 410)
                },
                onTogglePopup: () => {
                    if (intercomHasOpened) {
                        methods.onCloseIntercom()
                    } else {
                        if ($(".popup-intercom").css('display') === 'none') {
                            $(".popup-intercom").css('display', 'block');
                        } else {
                            $(".popup-intercom").css('display', 'none');
                        }
                        setTimeout(() => {
                            intercomHasOpened = true;
                            $(".btn-intercom-icon").removeClass("btn-intercom-close");
                            $(".btn-intercom-icon").addClass("btn-intercom-open");
                            $(".popup-intercom").addClass("popup-intercom-open");


                            nextTick(() => {
                                chatBox.value.scrollTop = chatBox.value
                                    .scrollHeight;
                            });
                        }, 100)
                    }
                },
                triggerFileUpload: () => {
                    // Memicu klik pada elemen input file yang disembunyikan
                    fileInput.value.click();
                },
                handleFileChange: (event) => {
                    const files = event.target.files;

                    Array.from(files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            // Menyimpan gambar yang dipilih ke previewImages
                            data.previewImages.push({
                                src: e.target.result,
                                file
                            });
                        };
                        reader.readAsDataURL(file);
                    });
                },
                removeImage: (index) => {
                    // Menghapus gambar dari array preview
                    data.previewImages.splice(index, 1);
                },
                adjustTextareaHeight: () => {
                    const textarea = chatTextarea.value;

                    // If the scrollHeight is greater than the current height, adjust it
                    const newHeight = textarea.scrollHeight;

                    // Jangan ubah height jika scrollHeight masih cukup untuk satu baris
                    if (newHeight > 30) { // 30px is the min-height for one row
                        if (data.message.length > 25) {
                            textarea.style.height = `${newHeight}px`;
                        } else {
                            textarea.style.height = `30px`;
                        }
                    } else {
                        textarea.style.height = '30px'; // Tinggi tetap untuk satu baris
                    }
                },
                sendMessage: () => {
                    if (data.conversation_mode !== 'default') {
                        data.conversation_mode = 'default';
                    }

                    if (!data.message && data.previewImages.length === 0)
                        return; // Jangan kirim jika kosong

                    // Variable untuk menyimpan isi pesan yang akan dikirim
                    let combinedMessage = '';

                    // Jika ada pesan teks, konversi ke HTML
                    if (data.message) {
                        combinedMessage += marked.parse(data.message); // Tambahkan teks ke variabel
                    }


                    // Jika ada gambar, tambahkan gambar ke dalam pesan yang sama
                    if (data.previewImages.length) {
                        data.previewImages.forEach(image => {
                            combinedMessage += `
                            <a href="${image.src}" data-lightbox="image-preview" data-title="Uploaded Image">
                                <img src="${image.src}" alt="Uploaded Image" class="uploaded-image" />
                            </a>`;
                        });
                    }

                    // Hanya tambahkan satu pesan jika ada teks atau gambar, bukan dua pesan terpisah
                    if (combinedMessage) {
                        data.messages.push({
                            id: Date.now(),
                            sender: 'You',
                            content: combinedMessage,
                            created_at: new Date().toISOString(),
                        });
                    }

                    // Jika pesan hanya gambar (tanpa teks)
                    if (data.previewImages.length && !data.message) {
                        data.previewImages.forEach(image => {
                            const fileMessage =
                                `<img src="${image.src}" alt="Uploaded Image" class="uploaded-image" />`;
                            data.messages.push({
                                id: Date.now(),
                                sender: 'You',
                                content: fileMessage,
                                created_at: new Date().toISOString(),
                            });
                        });
                    }

                    setTimeout(() => {
                        // Tambahkan pesan dummy "Sedang mengetik..." dari AI
                        const typingMessageId = Date.now();
                        data.messages.push({
                            id: typingMessageId,
                            sender: '<span style="color: #425c93"><i class="fa-solid fa-robot"></i> Randu AI</span>',
                            content: 'Sedang mengetik...',
                            created_at: new Date().toISOString(),
                        });

                        // Membuat FormData untuk mengirim teks dan file
                        const formData = new FormData();
                        formData.append('conversation_key', data.conversation_key);

                        // Tambahkan pesan teks jika ada
                        if (data.message) {
                            formData.append('message', data.message);
                        }

                        // Tambahkan file gambar jika ada
                        data.previewImages.forEach((image, index) => {
                            formData.append(`files[${index}]`, image.file);
                        });

                        setTimeout(() => {
                            // Kosongkan pesan dan preview gambar setelah dikirim
                            data.message = '';
                            data.previewImages = [];
                        }, 100)

                        // Kirim pesan ke API Laravel
                        axios.post('/v1/chat-ai', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                const botReplyHTML = marked.parse(response.data.reply);

                                // Temukan pesan "Sedang mengetik..." berdasarkan ID-nya dan update dengan balasan dari API
                                const typingMessageIndex = data.messages.findIndex(msg =>
                                    msg.id ===
                                    typingMessageId);
                                if (typingMessageIndex !== -1) {
                                    data.messages[typingMessageIndex].content =
                                        botReplyHTML;
                                    data.messages[typingMessageIndex].created_at =
                                        new Date().toISOString()
                                }

                                // Scroll ke bawah setelah pesan baru dirender
                                nextTick(() => {
                                    chatBox.value.scrollTop = chatBox.value
                                        .scrollHeight;
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error.response);

                                // Hapus pesan "Sedang mengetik..." berdasarkan ID-nya
                                const typingMessageIndex = data.messages.findIndex(msg =>
                                    msg.id ===
                                    typingMessageId);
                                if (typingMessageIndex !== -1) {
                                    data.messages[typingMessageIndex].content =
                                        error.response.data.message;
                                }

                                // Scroll ke bawah setelah pesan baru dirender
                                nextTick(() => {
                                    chatBox.value.scrollTop = chatBox.value
                                        .scrollHeight;
                                });
                            });
                    }, 10)
                },
                generateRandomString: (length) => {
                    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    let result = '';
                    for (let i = 0; i < length; i++) {
                        result += characters.charAt(Math.floor(Math.random() * characters.length));
                    }
                    return result;
                },
                onCreateNewConversation: () => {
                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/new-conversation',
                        params: {
                            conversation_key: data.conversation_key
                        }
                    }).then((response) => {
                        data.messages.length = 0
                        data.conversation_key = methods.generateRandomString(
                            16); // Generate dan simpan
                        localStorage.setItem('randuai_conversation_key', data.conversation_key);
                    })
                },
                onShowingConversation: () => {
                    data.conversation_mode = 'histories';
                    data.conversation_list_is_progress = true;

                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/conversation-list'
                    }).then(function(response) {
                        data.conversation_list = response.data.data;
                        data.conversation_list_is_progress = false;
                    })
                },
                onShowingConversationFav: () => {
                    data.conversation_favs.length = 0
                    data.conversation_mode = 'favorites';
                    data.conversation_list_is_progress = true;
                    methods.onLoadConversationFavCtg()

                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/fav-conversation'
                    }).then(function(response) {
                        data.conversation_favs = response.data.data;
                        // data.conversation_list_is_progress = false;
                        setTimeout(() => {
                            methods.onSelectFavorite()
                        }, 500);
                    })
                },
                onClosingConversation: () => {
                    data.conversation_mode = 'default';
                },
                onClickDetailConversation: (conversation_key) => {
                    data.conversation_key = conversation_key
                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/conversation-detail',
                        params: {
                            conversation_key
                        }
                    }).then((response) => {
                        data.messages.length = 0
                        data.conversation_mode = 'default'
                        response.data.data.forEach((dt) => {
                            data.messages.push({
                                id: dt.id,
                                sender: dt.role === 'system' ?
                                    '<span style="color: #425c93"><i class="fa-solid fa-robot"></i> Randu AI</span>' :
                                    'You',
                                content: marked.parse(dt.content),
                                created_at: dt.created_at,
                            });
                        })

                        nextTick(() => {
                            chatBox.value.scrollTop = chatBox.value
                                .scrollHeight;
                        });
                    })
                },
                onLoadConversationFavCtg: () => {
                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/fav-conversation-ctg'
                    }).then((response) => {
                        data.conversation_fav_ctgs = response.data.data;
                    })
                },
                onSelectFavorite: () => {
                    const category = $('#axy').val() || 'basic';
                    data.conversation_list_is_progress = true;

                    axios({
                        method: 'GET',
                        url: '/v1/chat-ai/fav-conversation',
                        params: {
                            category
                        }
                    }).then(function(response) {
                        data.conversation_favs = response.data.data;
                        data.conversation_list_is_progress = false;
                    })
                },
                onSelectFavConversation: (question) => {
                    data.message = question
                }
            }

            const updateHeight = () => {
                if (popupIntercomLayout.value) {
                    const height = data.hasIntro ? '90px' : '300px';
                    popupIntercomLayout.value.style.setProperty('--dynamic-height', height);
                }
            };

            watch(() => data.hasIntro, updateHeight);
            watch(() => data.message, (newValue) => {
                if (newValue === '') {
                    const textarea = chatTextarea.value
                    textarea.style.height = '30px';
                }
            })

            onMounted(() => {
                methods.onLoadConversationFavCtg()
                // const hasInit = localStorage.getItem('intercom-init')
                // if (hasInit) {
                //     // Lakukan sesuatu jika 'intercom-init' ada di localStorage
                //     console.log('Intercom telah diinisialisasi:', hasInit);
                //     data.hasIntro = true
                // } else {
                //     // Lakukan sesuatu jika 'intercom-init' tidak ada di localStorage
                //     console.log('Intercom belum diinisialisasi');
                // }
                updateHeight();

                // Cek localStorage untuk conversation_key
                const storedKey = localStorage.getItem('randuai_conversation_key');
                if (storedKey) {
                    data.conversation_key = storedKey; // Masukkan ke data.conversation_key
                    methods.onClickDetailConversation(storedKey)
                } else {
                    data.conversation_key = methods.generateRandomString(16); // Generate dan simpan
                    localStorage.setItem('randuai_conversation_key', data
                        .conversation_key); // Simpan di localStorage
                }

                const textarea = chatTextarea.value;
                textarea.addEventListener('paste', methods.onPaste);
            })

            return {
                data,
                methods,
                popupIntercomLayout,
                fileInput,
                chatTextarea,
                chatBox,
                moment
            }
        }
    }).mount('#app-intercom');
</script>
