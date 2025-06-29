<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-blue-600 text-white p-4">
                    <h1 class="text-xl font-semibold">Laravel Real-time Chat</h1>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            <span id="connectionStatus">Connecting...</span>
                        </span>
                        <span class="mx-2">|</span>
                        <span id="channelName">general</span>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messagesContainer" class="h-[500px] overflow-y-auto p-4 space-y-4 bg-gray-50">
                    <div class="text-center text-gray-500 py-4">
                        Loading messages...
                    </div>
                </div>

                <!-- Input Area -->
                <div class="p-4 bg-white border-t">
                    <form id="messageForm" class="flex space-x-4">
                        <input 
                            type="text" 
                            id="messageInput"
                            class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Type your message..."
                            autocomplete="off"
                        >
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        >
                            Send
                        </button>
                    </form>
                    <div id="typingIndicator" class="text-sm text-gray-500 mt-2 h-5"></div>
                </div>
            </div>

            <!-- Error Toast -->
            <div 
                id="errorToast" 
                class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full transition-transform duration-300 ease-in-out"
                role="alert"
            >
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="errorMessage"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            const messageForm = document.getElementById('messageForm');
            const messageInput = document.getElementById('messageInput');
            const connectionStatus = document.getElementById('connectionStatus');
            const errorToast = document.getElementById('errorToast');
            const errorMessage = document.getElementById('errorMessage');
            
            let channel = 'general'; // تغيير من 'connect' إلى 'general'
            let isConnected = false;

            // Initialize Echo connection
               // Initialize Echo connection
               try{
                window.Echo = new Echo({
                    broadcaster: 'pusher',
                    key: '6fd0240d3f0724aded6f',
                    cluster: 'eu',
                    encrypted: true
                });
               }catch(e){
                console.log(e);
               }
    if (window.Echo) {
            window.Echo.connector?.socket?.on('connect', () => {
                isConnected = true;
                connectionStatus.textContent = 'Connected';
                connectionStatus.previousElementSibling.classList.replace('bg-yellow-400', 'bg-green-400');

                // Subscribe to channel after connection
                window.Echo.channel(`chat.${channel}`)
                    .listen('.message.sent', (event) => {
                        console.log('New message:', event);
                        appendMessage(event);
                    });
            });

            window.Echo.connector?.socket?.on('disconnect', () => {
                isConnected = false;
                connectionStatus.textContent = 'Disconnected';
                connectionStatus.previousElementSibling.classList.replace('bg-green-400', 'bg-yellow-400');
            });

            window.Echo.connector.socket.on('error', (error) => {
                console.error('Connection error:', error);
                showError('Connection error: ' + error.message);
            });
        } else {
            showError('Chat service not available');
        }

            // Load existing messages
            fetchMessages();

            // Handle message submission
            messageForm.addEventListener('submit', async (e) => {
                
                e.preventDefault();
                
                const content = messageInput.value.trim();
                if (!content || !isConnected) return;

                try {
                    const response = await fetch('/api/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ 
                            content,
                            channel
                        })
                    });

                    if (!response.ok) {

                        throw new Error('Failed to send message');
                    }

                    messageInput.value = '';

                } catch (error) {
                    showError('Failed to send message. Please try again.');
                }
            });

            async function fetchMessages() {
                try {
                    const response = await fetch(`/api/chat/messages/${channel}`);
                    if (!response.ok) throw new Error('Failed to fetch messages');
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        messagesContainer.innerHTML = '';
                        data.data.forEach(appendMessage);
                        scrollToBottom();
                    }
                } catch (error) {
                    showError('Failed to load messages. Please refresh the page.');
                }
            }

            function appendMessage(message) {
                const isCurrentUser = message.user_id === 1; // Replace with actual user check
                
                const messageElement = document.createElement('div');
                messageElement.className = `flex ${isCurrentUser ? 'justify-end' : 'justify-start'}`;
                
                messageElement.innerHTML = `
                    <div class="max-w-[70%] break-words">
                        <div class="text-sm text-gray-500 mb-1 ${isCurrentUser ? 'text-right' : ''}">
                            ${message.user_name || 'Anonymous'} • ${message.formatted_time}
                        </div>
                        <div class="rounded-lg px-4 py-2 ${
                            isCurrentUser 
                                ? 'bg-blue-600 text-white' 
                                : 'bg-gray-200 text-gray-900'
                        }">
                            ${escapeHtml(message.content)}
                        </div>
                    </div>
                `;

                messagesContainer.appendChild(messageElement);
                scrollToBottom();
            }

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function showError(message) {
                errorMessage.textContent = message;
                errorToast.classList.remove('translate-y-full');
                
                setTimeout(() => {
                    errorToast.classList.add('translate-y-full');
                }, 5000);
            }

            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "<")
                    .replace(/>/g, ">")
                    .replace(/"/g, "/")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</body>
</html>
