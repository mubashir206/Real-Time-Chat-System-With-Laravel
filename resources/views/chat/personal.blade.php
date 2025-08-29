@extends('layouts.app')

@section('title', 'Personal Chat')

@section('header-title', 'Personal Chat')

@section('content')
    <div class="content-card w-100">
        <div class="row g-0">
            <!-- User List (Left Side) -->
            <div class="col-md-4 user-list">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Chats</h3>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                        <i class="fas fa-plus"></i> New Chat
                    </button>
                </div>
                <div id="conversation-list" class="list-group">
                    <!-- Conversations loaded here -->
                </div>
            </div>
            <!-- Chat Box (Right Side) -->
            <div class="col-md-8 chat-box">
                <div id="chat-header" class="chat-header d-flex align-items-center p-3 border-bottom" style="display: none;">
                    <div id="selected-avatar" class="rounded-circle me-2 d-flex align-items-center justify-content-center"
                         style="width: 40px; height: 40px; background: #3498db; color: #ffffff; font-size: 0.9rem;">
                    </div>
                    <h4 id="selected-name" class="mb-0"></h4>
                </div>
                <div id="chat-body" class="chat-body p-3" style="height: 400px; overflow-y: auto; display: none;">
                    <!-- Messages loaded here -->
                </div>
                <div id="chat-footer" class="chat-footer p-3 border-top" style="display: none;">
                    <form id="send-message-form" class="d-flex">
                        <input type="hidden" id="conversation-id" value="">
                        <input type="text" id="message-input" class="form-control me-2" placeholder="Type a message...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                <div class="d-flex align-items-center justify-content-center h-100 d-none" id="select-chat-prompt">
                    <div class="text-center">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Select a conversation to start chatting</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Conversation Modal -->
    <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newConversationLabel">Start New Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="other-user-id">Select User</label>
                        <select id="other-user-id" class="form-control">
                            <option value="">Select a user</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="create-conversation-btn">Start Chat</button>
                </div>
            </div>
        </div>
    </div>

    <link href="{{ asset('css/personalChat.css') }}" rel="stylesheet">
    {{-- <script src="{{ asset('js/personalChat.js') }}"></script> --}}
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const authUserId = {{ Auth::id() }};
    let currentChannel = null;
    let typingTimeout = null;
    let isTyping = false;

    // Initialize Pusher
    const pusher = new Pusher('864e679bfe2ca3ff2476', {
        cluster: 'ap2'
    });

    // Load users for modal
    function loadUsers() {
        fetch('/show-user', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Users response:', data);
            const select = document.getElementById('other-user-id');
            select.innerHTML = '<option value="">Select a user</option>';

            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No users found</option>';
                alert('No other users found. Please add users to start a chat.');
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            document.getElementById('other-user-id').innerHTML = '<option value="">Error loading users</option>';
            alert('Failed to load users. Check console for details.');
        });
    }

    // Load conversations
    function loadConversations() {
        const listContainer = document.getElementById('conversation-list');
        listContainer.innerHTML = '<p class="text-center p-3">Loading conversations...</p>';

        fetch('/conversations', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Conversations response:', data);
            listContainer.innerHTML = '';

            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(conv => {
                    const otherUser = conv.user_one_id === authUserId ? conv.user_two : conv.user_one;
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';

                    item.onclick = function(e) {
                        e.preventDefault();
                        loadMessages(conv.id, otherUser.name);
                        document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                    };

                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px; background: #3498db; color: #ffffff; font-size: 0.9rem;">
                                ${otherUser.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <strong>${otherUser.name}</strong>
                                <small class="d-block text-muted">Click to chat</small>
                            </div>
                        </div>
                    `;
                    listContainer.appendChild(item);
                });
            } else {
                listContainer.innerHTML = '<p class="text-muted p-3 text-center">No conversations yet.<br>Click "New Chat" to start!</p>';
            }
        })
        .catch(error => {
            console.error('Error loading conversations:', error);
            listContainer.innerHTML = '<p class="text-danger p-3 text-center">Error loading conversations</p>';
            alert('Failed to load conversations. Check console for details.');
        });
    }

    // Unsubscribe from current channel
    function unsubscribeFromCurrentChannel() {
        if (currentChannel) {
            pusher.unsubscribe(currentChannel.name);
            currentChannel = null;
            console.log('Unsubscribed from previous channel');
        }
    }

    // Send typing start event
    function sendTypingStart(conversationId) {
        if (!isTyping) {
            isTyping = true;
            fetch('/conversations/typing/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    conversation_id: parseInt(conversationId)
                })
            })
            .catch(error => console.error('Error sending typing start:', error));
        }
    }

    // Send typing stop event
    function sendTypingStop(conversationId) {
        if (isTyping) {
            isTyping = false;
            fetch('/conversations/typing/stop', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    conversation_id: parseInt(conversationId)
                })
            })
            .catch(error => console.error('Error sending typing stop:', error));
        }
    }

    // Show typing indicator
    function showTypingIndicator(userName) {
        const chatBody = document.getElementById('chat-body');

        // Remove existing typing indicator
        const existingIndicator = chatBody.querySelector('.typing-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }

        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator d-flex justify-content-start mb-2';
        typingDiv.innerHTML = `
            <div class="message-bubble received" style="background-color: #f0f0f0; border: 1px solid #ddd;">
                <div class="typing-animation d-flex align-items-center">
                    <span class="me-2">${userName} is typing</span>
                    <div class="typing-dots">
                        <span>.</span>
                        <span>.</span>
                        <span>.</span>
                    </div>
                </div>
            </div>
        `;

        chatBody.appendChild(typingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Hide typing indicator
    function hideTypingIndicator() {
        const chatBody = document.getElementById('chat-body');
        const typingIndicator = chatBody.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Mark messages as seen
    function markMessagesAsSeen(conversationId) {
        fetch(`/conversations/${conversationId}/mark-seen`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.updated_count > 0) {
                console.log(`Marked ${data.updated_count} messages as seen`);
                // Only refresh UI if messages were actually updated
                refreshMessagesReadStatus(conversationId);
            } else {
                console.log('No new messages to mark as seen');
                // Still refresh UI to ensure correct tick display for previously seen messages
                refreshMessagesReadStatus(conversationId);
            }
        })
        .catch(error => {
            console.error('Error marking messages as seen:', error);
        });
    }

    // Update message read receipts in UI - Updated to handle all sent messages
    function updateMessageReadReceipts() {
        const chatBody = document.getElementById('chat-body');
        const sentMessages = chatBody.querySelectorAll('.message-bubble.sent');

        sentMessages.forEach(messageEl => {
            const tickIcon = messageEl.querySelector('small i');
            if (tickIcon && tickIcon.classList.contains('fa-check') && !tickIcon.classList.contains('fa-check-double')) {
                // Change single tick to double tick
                tickIcon.classList.remove('fa-check');
                tickIcon.classList.add('fa-check-double');
                console.log('Updated message to double tick (seen)');
            }
        });
    }

    // Refresh messages to get updated read status - FIXED FUNCTION
    function refreshMessagesReadStatus(conversationId) {
        fetch(`/conversations/${conversationId}/messages`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                const chatBody = document.getElementById('chat-body');
                data.data.forEach(msg => {
                    if (msg.user_id === authUserId) {
                        const messageElement = chatBody.querySelector(`[data-message-id="${msg.id}"]`);
                        if (messageElement) {
                            const tickIcon = messageElement.querySelector('small i');
                            if (tickIcon) {
                                if (msg.is_seen) {
                                    if (!tickIcon.classList.contains('fa-check-double')) {
                                        tickIcon.classList.remove('fa-check');
                                        tickIcon.classList.add('fa-check-double');
                                        console.log(`Message ${msg.id} updated to seen (double tick)`);
                                    }
                                } else {
                                    if (tickIcon.classList.contains('fa-check-double')) {
                                        tickIcon.classList.remove('fa-check-double');
                                        tickIcon.classList.add('fa-check');
                                        console.log(`Message ${msg.id} updated to unseen (single tick)`);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error refreshing message read status:', error);
        });
    }

    // Load messages
    function loadMessages(conversationId, userName) {
        document.getElementById('conversation-id').value = conversationId;
        document.getElementById('selected-name').textContent = userName;
        document.getElementById('selected-avatar').textContent = userName.charAt(0).toUpperCase();

        document.getElementById('select-chat-prompt').style.display = 'none';
        document.getElementById('chat-header').style.display = 'flex';
        document.getElementById('chat-body').style.display = 'block';
        document.getElementById('chat-footer').style.display = 'block';

        const chatBody = document.getElementById('chat-body');
        chatBody.innerHTML = '<p class="text-center">Loading messages...</p>';

        // Unsubscribe from previous channel before subscribing to new one
        unsubscribeFromCurrentChannel();

        fetch(`/conversations/${conversationId}/messages`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            chatBody.innerHTML = '';
            if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(msg => {
                    addMessageToChat(msg);
                });
            } else {
                chatBody.innerHTML = '<p class="text-muted text-center">No messages yet. Start the conversation!</p>';
            }
            chatBody.scrollTop = chatBody.scrollHeight;

            // Mark messages as seen when loading conversation
            markMessagesAsSeen(conversationId);
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            chatBody.innerHTML = '<p class="text-danger text-center">Error loading messages</p>';
        });

        // Subscribe to Pusher channel for this conversation
        const channelName = 'conversation.' + conversationId;
        currentChannel = pusher.subscribe(channelName);
        console.log('Subscribed to channel:', channelName);

        // Listen for messages
        currentChannel.bind('message.sent', function(data) {
            console.log("New message received:", data);

            if (data.conversation_id == conversationId) {
                console.log("Adding message to chat:", data);
                hideTypingIndicator();
                addMessageToChat(data);

                const chatBody = document.getElementById('chat-body');
                chatBody.scrollTop = chatBody.scrollHeight;

                // Mark new received message as seen
                setTimeout(() => {
                    markMessagesAsSeen(conversationId);
                }, 500);

                loadConversations();
            }
        });

        // Listen for typing events
        currentChannel.bind('user.typing', function(data) {
            console.log("Typing event received:", data);

            if (data.conversation_id == conversationId && data.user_id !== authUserId) {
                if (data.is_typing) {
                    showTypingIndicator(data.user_name);
                } else {
                    hideTypingIndicator();
                }
            }
        });

        // Listen for messages seen events - ENHANCED WITH IMMEDIATE REFRESH
        currentChannel.bind('messages.seen', function(data) {
            console.log("Messages seen event received:", data);

            if (data.conversation_id == conversationId && data.seen_by_user_id !== authUserId) {
                // Immediately refresh read status from server to get accurate data
                refreshMessagesReadStatus(conversationId);

                console.log("Refreshed read receipts due to messages.seen event");
            }
        });

        // CRITICAL: Always refresh read status when opening conversation
        // This handles all cases where messages were marked as seen while chat was closed
        setTimeout(() => {
            refreshMessagesReadStatus(conversationId);
            console.log("Initial read status refresh on conversation load");
        }, 1000);
    }

    // Add message to chat - UPDATED TO INCLUDE MESSAGE ID
    function addMessageToChat(message) {
        const chatBody = document.getElementById('chat-body');

        // Remove "No messages yet" placeholder if it exists
        const placeholder = chatBody.querySelector('.text-muted.text-center');
        if (placeholder) {
            chatBody.innerHTML = '';
        }

        const messageDiv = document.createElement('div');
        const isSent = message.user_id === authUserId;

        messageDiv.className = `d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'} mb-2`;
        messageDiv.setAttribute('data-message-id', message.id); // Add message ID as data attribute
        messageDiv.innerHTML = `
            <div class="message-bubble ${isSent ? 'sent' : 'received'}">
                <p class="mb-1">${message.message}</p>
                <small style="opacity: 0.7;">
                    ${new Date(message.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}
                    ${isSent ? (message.is_seen ? '<i class="fas fa-check-double ms-1"></i>' : '<i class="fas fa-check ms-1"></i>') : ''}
                </small>
            </div>
        `;

        chatBody.appendChild(messageDiv);
        console.log('Message added to chat UI with ID:', message.id);
    }

    // Create conversation
    document.getElementById('create-conversation-btn').addEventListener('click', function() {
        const otherUserId = document.getElementById('other-user-id').value;

        if (!otherUserId) {
            alert('Please select a user');
            return;
        }

        fetch('/show-user', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(userData => {
            if (!userData.success || !Array.isArray(userData.data)) {
                throw new Error('Invalid user data response');
            }
            const selectedUser = userData.data.find(user => user.id === parseInt(otherUserId));
            if (!selectedUser) {
                throw new Error('Selected user not found');
            }

            return fetch('/conversations/find-or-create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ other_user_id: parseInt(otherUserId) })
            })
            .then(response => {
                console.log('Raw response:', response);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}, Status Text: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Parsed conversation response:', data);

                if (data && data.success && data.data && data.data.id) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newConversationModal'));
                    modal.hide();

                    loadConversations();
                    loadMessages(data.data.id, selectedUser.name);
                } else {
                    console.error('Conversation creation failed:', {
                        success: data.success,
                        data: data.data,
                        message: data.message || 'Unknown error'
                    });
                    alert('Failed to create conversation: ' + (data.message || 'Invalid response format'));
                }
            });
        })
        .catch(error => {
            console.error('Error creating conversation:', error);
            alert('Failed to create conversation. Check console for details.');
        });
    });

    // Handle typing in message input
    document.getElementById('message-input').addEventListener('input', function() {
        const conversationId = document.getElementById('conversation-id').value;
        if (!conversationId) return;

        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }

        sendTypingStart(conversationId);

        typingTimeout = setTimeout(function() {
            sendTypingStop(conversationId);
        }, 1000);
    });

    // Send message
    document.getElementById('send-message-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const conversationId = document.getElementById('conversation-id').value;
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();

        if (!conversationId) {
            alert('Please select a conversation');
            return;
        }
        if (!message) {
            alert('Please enter a message');
            return;
        }

        sendTypingStop(conversationId);

        if (typingTimeout) {
            clearTimeout(typingTimeout);
            typingTimeout = null;
        }

        fetch('/conversations/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                conversation_id: parseInt(conversationId),
                message: message
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Send message response:', data);

            if (data.success && data.data) {
                messageInput.value = '';
            } else {
                alert('Failed to send message: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message. Check console for details.');
        });
    });

    // Load users when modal opens
    document.getElementById('newConversationModal').addEventListener('shown.bs.modal', loadUsers);

    // Handle window focus to mark messages as seen - UPDATED
    window.addEventListener('focus', function() {
        const conversationId = document.getElementById('conversation-id').value;
        if (conversationId) {
            markMessagesAsSeen(conversationId);
            // Also refresh read status for sender
            setTimeout(() => {
                refreshMessagesReadStatus(conversationId);
            }, 500);
        }
    });

    // Handle chat body click to mark messages as seen - UPDATED
    document.addEventListener('click', function(e) {
        if (e.target.closest('#chat-body')) {
            const conversationId = document.getElementById('conversation-id').value;
            if (conversationId) {
                markMessagesAsSeen(conversationId);
                setTimeout(() => {
                    refreshMessagesReadStatus(conversationId);
                }, 500);
            }
        }
    });

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        const conversationId = document.getElementById('conversation-id').value;
        if (conversationId) {
            sendTypingStop(conversationId);
        }
        unsubscribeFromCurrentChannel();
    });

    // Initial load
    loadConversations();
});
    </script>
@endsection
