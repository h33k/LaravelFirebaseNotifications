<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    @if (Auth::user()->bearer_token)
                        <div class="pt-2 text-gray-900">
                            <p>Bearer Token to register device:</p>
                            <code id="user-token">{{ Auth::user()->bearer_token }}</code>

                            <div class="mt-4">
                                <button id="register-btn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Register this device via api
                                </button>
                                <div id="register-status" class="mt-2 text-sm text-gray-700"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
    <script type="module">
        import {initializeApp} from 'https://www.gstatic.com/firebasejs/11.9.1/firebase-app.js';
        import {
            getMessaging,
            getToken,
            onMessage
        } from 'https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging.js';


        await import("{{ asset('firebaseConfig.js') }}");

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        window.addEventListener('load', function () {
            document.getElementById('register-btn').addEventListener('click', async () => {
                const registerStatus = document.getElementById('register-status');
                registerStatus.innerText = "Process register...";

                try {
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

                    const token = await getToken(messaging, {
                        vapidKey: 'BJ7Aukmqt4KvPahXPGtVkUUzOmWWBo9nfjRzjfd1NxbrRMtHmLiFRP015fLY57i-7Jwz2Z-dWTz0xx29Bytzml8',
                        serviceWorkerRegistration: registration
                    });

                    const bearerToken = document.getElementById('user-token').innerText;
                    const deviceUid = localStorage.getItem('device_uid') || crypto.randomUUID();
                    localStorage.setItem('device_uid', deviceUid);

                    const response = await fetch('/api/devices/register', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + bearerToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            device_uid: deviceUid,
                            fcm_token: token,
                            name: navigator.userAgent
                        }),
                    });

                    const text = await response.text();
                    let result;

                    try {
                        result = JSON.parse(text);
                    } catch (e) {
                        registerStatus.innerText = "Error: server return not JSON: " + text;
                        console.error("JSON parse error:", e);
                        return;
                    }

                    if (response.ok) {
                        registerStatus.innerText = "Device registered successfully!";
                    } else {
                        const errors = result.errors
                            ? Object.values(result.errors).flat().join(', ')
                            : result.message || "Error";
                        registerStatus.innerText = "Registration error: " + errors;
                    }

                } catch (error) {
                    console.error("Error:", error);
                    registerStatus.innerText = "Registration error: " + error.message;
                }
            });

            onMessage(messaging, (payload) => {
                console.log("New notification:", payload);

                const title = "New notification!";
                const body = payload.data?.body ?? "Lorem ipsum";

                const backdrop = document.createElement("div");
                backdrop.className = `
                fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50
                `;

                        const modal = document.createElement("div");
                        modal.className = `
                bg-white rounded-lg shadow-xl max-w-xs w-full p-6 text-center
                animate-fade-in
                `;
                        modal.innerHTML = `
                <h2 class="text-xl font-semibold text-gray-800 mb-3">${title}</h2>
                <p class="text-gray-600 text-md mb-2">${body}</p>
                <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    OK
                </button>
                `;

                backdrop.appendChild(modal);
                document.body.appendChild(backdrop);

                modal.querySelector("button").addEventListener("click", () => {
                    backdrop.classList.add("opacity-0", "transition-opacity", "duration-300");
                    setTimeout(() => backdrop.remove(), 300);
                });
            });


        });
    </script>
</x-app-layout>
