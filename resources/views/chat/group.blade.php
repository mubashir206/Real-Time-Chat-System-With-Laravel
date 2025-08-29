
@extends('layouts.app')

@section('title', 'Group Chat')

@section('header-title', 'Group Chat')

@section('content')
<div class="content-card w-100">
    <div class="row g-0">
        <!-- Group List (Left Side) -->
        <div class="col-md-4 user-list">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Groups</h3>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newGroupModal"><i class="fas fa-plus"></i> New Group</button>
            </div>
            <div id="group-list" class="list-group">
                <!-- Groups loaded via JS -->
            </div>
        </div>
        <!-- Chat Box (Right Side) -->
        <div class="col-md-8 chat-box">
            <div id="chat-header" class="chat-header d-flex align-items-center p-3 border-bottom" style="display: none;">
                <div id="selected-avatar" class="rounded-circle me-2 d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; background: #3498db; color: #ffffff; font-size: 0.9rem;">
                </div>
                <div class="d-flex flex-column">
                    <h4 id="selected-name" class="mb-0"></h4>
                    <small id="admin-name" class="text-muted"></small>
                </div>
                <div class="ms-auto d-flex">
                    <button class="btn btn-sm btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#addMemberModal" id="add-member-btn" style="display: none;"><i class="fas fa-user-plus"></i></button>
                    <button class="btn btn-sm btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#groupMembersModal" id="members-btn" style="display: none;"><i class="fas fa-users"></i></button>
                    <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#groupSettingsModal" id="settings-btn" style="display: none;"><i class="fas fa-cog"></i></button>
                </div>
            </div>
            <div id="chat-body" class="chat-body p-3" style="height: 400px; overflow-y: auto; display: none;">
                <!-- Messages loaded via JS -->
            </div>
            <div id="chat-footer" class="chat-footer p-3 border-top" style="display: none;">
                <form id="send-message-form" class="d-flex">
                    <input type="hidden" id="group-id" value="">
                    <input type="text" id="message-input" class="form-control me-2" placeholder="Type a message...">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
            <div id="private-group-message" class="p-3 text-center" style="display: none;">
                <p class="text-muted">This group is private. Only admins can send messages.</p>
            </div>
            <div class="d-flex align-items-center justify-content-center h-100 d-none" id="select-chat-prompt">
                <div class="text-center">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Select a group to start chatting</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Group Modal -->
<div class="modal fade" id="newGroupModal" tabindex="-1" aria-labelledby="newGroupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newGroupLabel">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="group-name">Group Name</label>
                    <input type="text" id="group-name" class="form-control" placeholder="Enter group name">
                </div>
                <div class="form-group mt-3">
                    <label for="group-type">Group Type</label>
                    <select id="group-type" class="form-control">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="create-group-btn">Create Group</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMemberLabel">Add Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="add-user-id">Select User</label>
                    <select id="add-user-id" class="form-control">
                        <option value="">Select a user</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add-member-btn-submit">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Group Members Modal -->
<div class="modal fade" id="groupMembersModal" tabindex="-1" aria-labelledby="groupMembersLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupMembersLabel">Group Members</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="group-members-list" class="list-group">
                    <!-- Members loaded via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Group Settings Modal -->
<div class="modal fade" id="groupSettingsModal" tabindex="-1" aria-labelledby="groupSettingsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupSettingsLabel">Group Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="group-type-setting">Group Type</label>
                    <select id="group-type-setting" class="form-control">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update-group-type-btn">Update</button>
            </div>
        </div>
    </div>
</div>
    <link href="{{ asset('css/groupChat.css') }}" rel="stylesheet">

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = '{{ csrf_token() }}';
    const authUserId = {{ Auth::id() }};
    const authUserName = '{{ Auth::user()->name }}';
    let currentGroupId = null;
    let isAdmin = false;
    let groupType = 'public';
    let currentChannel = null;
    let typingTimeout = null;
    let isTyping = false;
    let pendingMessages = new Map(); // Track pending messages by their temp ID

    // Initialize Pusher
    const pusher = new Pusher('864e679bfe2ca3ff2476', {
        cluster: 'ap2'
    });

    // Update UI based on permissions
    function updateUIBasedOnPermissions() {
        document.getElementById('add-member-btn').style.display = isAdmin ? 'block' : 'none';
        document.getElementById('settings-btn').style.display = isAdmin ? 'block' : 'none';
        document.getElementById('members-btn').style.display = isAdmin ? 'block' : 'none';
        document.getElementById('chat-footer').style.display = (groupType === 'private' && !isAdmin) ? 'none' : 'block';
        document.getElementById('private-group-message').style.display = (groupType === 'private' && !isAdmin) ? 'block' : 'none';
        document.getElementById('group-type-setting').value = groupType;

        console.log('UI updated - isAdmin:', isAdmin, 'groupType:', groupType);
        console.log('Chat footer display:', document.getElementById('chat-footer').style.display);
        console.log('Private message display:', document.getElementById('private-group-message').style.display);
    }

    // Load users for add member modal
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
            const select = document.getElementById('add-user-id');
            select.innerHTML = '<option value="">Select a user</option>';
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No users found</option>';
                alert('No users available to add.');
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            document.getElementById('add-user-id').innerHTML = '<option value="">Error loading users</option>';
            alert('Failed to load users. Check console for details.');
        });
    }

    // Load group members for members modal
    function loadGroupMembers() {
        fetch(`/groups/${currentGroupId}/members`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Group members response:', data);
            const membersList = document.getElementById('group-members-list');
            membersList.innerHTML = '';
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(member => {
                    const item = document.createElement('div');
                    item.className = 'list-group-item d-flex align-items-center';
                    item.innerHTML = `
                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center"
                             style="width: 30px; height: 30px; background: #3498db; color: #ffffff; font-size: 0.8rem;">
                            ${member.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <strong>${member.name}</strong>
                            <small class="d-block text-muted">${member.role.charAt(0).toUpperCase() + member.role.slice(1)}</small>
                        </div>
                    `;
                    membersList.appendChild(item);
                });
            } else {
                membersList.innerHTML = '<p class="text-muted text-center">No members found.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading group members:', error);
            document.getElementById('group-members-list').innerHTML = '<p class="text-danger text-center">Error loading members</p>';
            alert('Failed to load group members. Check console for details.');
        });
    }

    // Load groups
    function loadGroups() {
        const listContainer = document.getElementById('group-list');
        listContainer.innerHTML = '<p class="text-center p-3">Loading groups...</p>';

        fetch('/my-groups', {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Groups response:', data);
            listContainer.innerHTML = '';
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(group => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.onclick = (e) => {
                        e.preventDefault();
                        currentGroupId = group.id;
                        isAdmin = group.members.some(m => m.user_id === authUserId && m.role === 'admin');
                        groupType = group.group_type;
                        updateUIBasedOnPermissions();
                        const adminName = group.creator ? group.creator.name : 'Unknown';
                        loadGroupMessages(group.id, group.name, adminName);
                        document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                    };
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #3498db; color: #ffffff; font-size: 0.9rem;">
                                ${group.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <strong>${group.name}</strong>
                                <small class="d-block text-muted">${group.group_type === 'public' ? 'Public' : 'Private'} Group</small>
                            </div>
                        </div>
                    `;
                    listContainer.appendChild(item);
                });
            } else {
                listContainer.innerHTML = '<p class="text-muted p-3 text-center">No groups yet.<br>Click "New Group" to start!</p>';
            }
        })
        .catch(error => {
            console.error('Error loading groups:', error);
            listContainer.innerHTML = '<p class="text-danger p-3 text-center">Error loading groups</p>';
            alert('Failed to load groups. Check console for details.');
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
    function sendTypingStart(groupId) {
        if (!isTyping) {
            isTyping = true;
            fetch(`/groups/${groupId}/typing/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .catch(error => console.error('Error sending typing start:', error));
        }
    }

    // Send typing stop event
    function sendTypingStop(groupId) {
        if (isTyping) {
            isTyping = false;
            fetch(`/groups/${groupId}/typing/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({})
            })
            .catch(error => console.error('Error sending typing stop:', error));
        }
    }

    // Show typing indicator
    function showTypingIndicator(userName) {
        const chatBody = document.getElementById('chat-body');
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
    function markMessagesAsSeen(groupId) {
        fetch(`/groups/${groupId}/mark-seen`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log(`Marked ${data.updated_count} messages as seen`);
            }
        })
        .catch(error => {
            console.error('Error marking messages as seen:', error);
        });
    }

    // Update message read receipts in UI
    function updateMessageReadReceipts() {
        const chatBody = document.getElementById('chat-body');
        const sentMessages = chatBody.querySelectorAll('.message-bubble.sent');
        sentMessages.forEach(messageEl => {
            const tickIcon = messageEl.querySelector('small i');
            if (tickIcon && tickIcon.classList.contains('fa-check') && !tickIcon.classList.contains('fa-check-double')) {
                tickIcon.classList.remove('fa-check');
                tickIcon.classList.add('fa-check-double');
                tickIcon.classList.remove('text-muted');
                tickIcon.classList.add('text-primary');
                console.log('Updated message to double tick (seen)');
            }
        });
    }

    // Create new group
    document.getElementById('create-group-btn').addEventListener('click', () => {
        const name = document.getElementById('group-name').value.trim();
        const type = document.getElementById('group-type').value;
        if (!name) {
            alert('Please enter a group name');
            return;
        }
        fetch('/groups', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name, group_type: type })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Create group response:', data);
            if (data.success && data.data) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('newGroupModal'));
                modal.hide();
                loadGroups();
                loadGroupMessages(data.data.id, data.data.name, authUserName);
            } else {
                alert('Failed to create group: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error creating group:', error);
            alert('Failed to create group. Check console for details.');
        });
    });

    // Add member to group
    document.getElementById('add-member-btn-submit').addEventListener('click', () => {
        const userId = document.getElementById('add-user-id').value;
        if (!userId) {
            alert('Please select a user');
            return;
        }
        fetch(`/groups/${currentGroupId}/members`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ user_id: parseInt(userId) })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Add member response:', data);
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addMemberModal'));
                modal.hide();
                alert(data.message || 'Member added successfully!');
                if (!data.message) {
                    loadGroups();
                }
            } else {
                alert('Failed to add member: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error adding member:', error);
            alert('Failed to add member. Check console for details.');
        });
    });

    // Update group type
    document.getElementById('update-group-type-btn').addEventListener('click', () => {
        const newType = document.getElementById('group-type-setting').value;
        fetch(`/groups/${currentGroupId}/privacy`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ group_type: newType })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Update group type response:', data);
            if (data.success && data.data) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('groupSettingsModal'));
                modal.hide();
                alert('Group type updated successfully!');
                // Update local variables
                groupType = data.data.group_type;
                // Update UI immediately for admin who made the change
                updateUIBasedOnPermissions();
                // Reload groups to show updated type
                loadGroups();
            } else {
                alert('Failed to update group type: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error updating group type:', error);
            alert('Failed to update group type. Check console for details.');
        });
    });

    // Load group messages
    function loadGroupMessages(groupId, name, adminName) {
        document.getElementById('group-id').value = groupId;
        document.getElementById('selected-name').textContent = name;
        document.getElementById('selected-avatar').textContent = name.charAt(0).toUpperCase();
        document.getElementById('admin-name').textContent = adminName ? `Admin: ${adminName}` : 'Admin: Unknown';
        document.getElementById('chat-header').style.display = 'flex';
        document.getElementById('chat-body').style.display = 'block';
        updateUIBasedOnPermissions();
        document.getElementById('select-chat-prompt').style.display = 'none';

        const chatBody = document.getElementById('chat-body');
        chatBody.innerHTML = '<p class="text-center">Loading messages...</p>';

        // Clear pending messages when switching groups
        pendingMessages.clear();

        unsubscribeFromCurrentChannel();

        fetch(`/groups/${groupId}/messages`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Messages response:', data);
            chatBody.innerHTML = '';
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(msg => {
                    addMessageToChat(msg);
                });
            } else {
                chatBody.innerHTML = '<p class="text-muted text-center">No messages yet. Start the conversation!</p>';
            }
            chatBody.scrollTop = chatBody.scrollHeight;

            const channelName = 'group.' + groupId;
            currentChannel = pusher.subscribe(channelName);
            console.log('Subscribed to channel:', channelName);

            // Listen for messages
            currentChannel.bind('message.sent', function(data) {
                console.log("New message received via Pusher:", data);
                if (data.group_id == groupId) {
                    hideTypingIndicator();

                    // Check if this is our own message that we sent (check pending messages)
                    let tempMessageFound = false;
                    for (let [tempId, messageInfo] of pendingMessages) {
                        if (messageInfo.content === data.message && data.user_id === authUserId) {
                            // This is our message - replace the temp message
                            const tempMessageElement = document.querySelector(`[data-message-id="${tempId}"]`);
                            if (tempMessageElement) {
                                // Update the temp message to become the real message
                                tempMessageElement.setAttribute('data-message-id', data.id);
                                const timeEl = tempMessageElement.querySelector('small');
                                timeEl.innerHTML = `${new Date(data.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })} <i class="fas fa-check ms-1"></i>`;
                                console.log(`Replaced temp message ${tempId} with real message ${data.id}`);

                                // Remove from pending messages
                                pendingMessages.delete(tempId);
                                tempMessageFound = true;
                                break;
                            }
                        }
                    }

                    // If no temp message found, this is a message from another user
                    if (!tempMessageFound && data.user_id !== authUserId) {
                        addMessageToChat(data);
                        console.log("Added new message from other user:", data.id);
                    }

                    chatBody.scrollTop = chatBody.scrollHeight;

                    // Mark messages as seen after a short delay
                    setTimeout(() => {
                        markMessagesAsSeen(groupId);
                    }, 500);

                    loadGroups();
                }
            });

            // Listen for typing events
            currentChannel.bind('user.typing', function(data) {
                console.log("Typing event received:", data);
                if (data.group_id == groupId && data.user_id !== authUserId) {
                    if (data.is_typing) {
                        showTypingIndicator(data.user_name);
                    } else {
                        hideTypingIndicator();
                    }
                }
            });

            // Listen for message seen events
            currentChannel.bind('messages.seen', function(data) {
                console.log("Messages seen event received:", data);
                if (data.group_id == groupId && data.user_id !== authUserId) {
                    updateMessageReadReceipts();
                }
            });

            // Listen for group privacy changes
            currentChannel.bind('group.privacy.changed', function(data) {
                console.log("Group privacy changed event received:", data);
                if (data.group_id == groupId) {
                    // Update local groupType variable
                    groupType = data.new_type;

                    // Update UI for all users (including non-admins)
                    updateUIBasedOnPermissions();

                    // Show notification to users about the change
                    const notificationText = data.new_type === 'private'
                        ? 'Group is now private. Only admins can send messages.'
                        : 'Group is now public. Everyone can send messages.';

                    // Add system message to chat
                    const systemMsg = document.createElement('div');
                    systemMsg.className = 'd-flex justify-content-center mb-2';
                    systemMsg.innerHTML = `
                        <div class="alert alert-info alert-sm mx-auto" role="alert" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">
                            <i class="fas fa-info-circle me-1"></i>${notificationText}
                        </div>
                    `;
                    chatBody.appendChild(systemMsg);
                    chatBody.scrollTop = chatBody.scrollHeight;

                    // Reload groups to show updated privacy type in the list
                    loadGroups();
                }
            });

            setTimeout(() => {
                markMessagesAsSeen(groupId);
            }, 500);
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            document.getElementById('chat-body').innerHTML = '<p class="text-danger text-center">Error loading messages</p>';
            alert('Failed to load messages. Check console for details.');
        });
    }

    // Add message to chat
    function addMessageToChat(msg) {
        const chatBody = document.getElementById('chat-body');
        const placeholder = chatBody.querySelector('.text-muted.text-center');
        if (placeholder) {
            chatBody.innerHTML = '';
        }
        const isSent = msg.user_id === authUserId;
        const div = document.createElement('div');
        div.className = `d-flex ${isSent ? 'justify-content-end' : 'justify-content-start'} mb-2`;
        div.setAttribute('data-message-id', msg.id);

        // Handle user name display - for temp messages, use current user name
        const userName = msg.user ? msg.user.name : (isSent ? authUserName : 'Unknown User');

        div.innerHTML = `
            <div class="message-bubble ${isSent ? 'sent' : 'received'}">
                <p class="mb-0"><strong>${userName}</strong>: ${msg.message}</p>
                <small class="text-muted d-block">
                    ${new Date(msg.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}
                    ${isSent ? '<i class="fas ' + (msg.is_seen ? 'fa-check-double text-primary' : 'fa-check text-muted') + ' ms-1"></i>' : ''}
                </small>
            </div>
        `;
        chatBody.appendChild(div);
        console.log('Message added to chat UI with ID:', msg.id);
    }

    // Send message in group with optimistic UI (NEW)
    document.getElementById('send-message-form').addEventListener('submit', (e) => {
        e.preventDefault();

        const groupId = document.getElementById('group-id').value;
        const messageInput = document.getElementById('message-input');
        const sendButton = e.target.querySelector('button[type="submit"]');
        const message = messageInput.value.trim();

        if (!groupId) {
            alert('Please select a group');
            return;
        }
        if (!message) {
            alert('Please enter a message');
            return;
        }
        if (sendButton.disabled) return;

        // Disable form to prevent multiple submissions
        messageInput.disabled = true;
        sendButton.disabled = true;

        // Stop typing indicator
        sendTypingStop(groupId);
        if (typingTimeout) {
            clearTimeout(typingTimeout);
            typingTimeout = null;
        }

        // Create unique temp ID and store message info
        const tempId = 'temp-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        pendingMessages.set(tempId, {
            content: message,
            timestamp: Date.now()
        });

        // Optimistic add with temp ID
        const tempMessage = {
            id: tempId,
            user_id: authUserId,
            message: message,
            created_at: new Date().toISOString(),
            is_seen: false,
            user: { name: authUserName } // Add user info for display
        };
        addMessageToChat(tempMessage);

        const chatBody = document.getElementById('chat-body');
        chatBody.scrollTop = chatBody.scrollHeight;
        messageInput.value = '';

        // Send message to server
        fetch(`/groups/${groupId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Send message response:', data);
            if (!data.success) {
                throw new Error(data.message || 'Failed to send message');
            }
            // The Pusher event will handle replacing the temp message
        })
        .catch(error => {
            console.error('Error sending message:', error);
            // Remove temp message on error
            const messageEl = chatBody.querySelector(`[data-message-id="${tempId}"]`);
            if (messageEl) {
                messageEl.remove();
                console.log('Removed temp message due to error:', tempId);
            }
            pendingMessages.delete(tempId);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            // Re-enable form
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        });
    });

    // Handle typing in message input
    document.getElementById('message-input').addEventListener('input', function() {
        const groupId = document.getElementById('group-id').value;
        if (!groupId) return;

        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }

        sendTypingStart(groupId);

        typingTimeout = setTimeout(function() {
            sendTypingStop(groupId);
        }, 1000);
    });

    // Clean up old pending messages (in case of network issues)
    setInterval(function() {
        const now = Date.now();
        for (let [tempId, messageInfo] of pendingMessages) {
            // Remove pending messages older than 30 seconds
            if (now - messageInfo.timestamp > 30000) {
                const messageEl = document.getElementById('chat-body').querySelector(`[data-message-id="${tempId}"]`);
                if (messageEl) {
                    messageEl.remove();
                    console.log('Cleaned up old temp message:', tempId);
                }
                pendingMessages.delete(tempId);
            }
        }
    }, 5000);

    // Load users when add member modal opens
    document.getElementById('addMemberModal').addEventListener('shown.bs.modal', loadUsers);

    // Load group members when members modal opens
    document.getElementById('groupMembersModal').addEventListener('shown.bs.modal', loadGroupMembers);

    // Handle window focus to mark messages as seen
    window.addEventListener('focus', function() {
        const groupId = document.getElementById('group-id').value;
        if (groupId) {
            markMessagesAsSeen(groupId);
        }
    });

    // Handle chat body click to mark messages as seen
    document.addEventListener('click', function(e) {
        if (e.target.closest('#chat-body')) {
            const groupId = document.getElementById('group-id').value;
            if (groupId) {
                markMessagesAsSeen(groupId);
            }
        }
    });

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        const groupId = document.getElementById('group-id').value;
        if (groupId) {
            sendTypingStop(groupId);
        }
        unsubscribeFromCurrentChannel();
    });

    // Initial load
    loadGroups();
});
</script>
@endsection
