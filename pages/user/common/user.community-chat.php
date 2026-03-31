<button class="chat-toggle-btn" id="chatToggleBtn" aria-label="Open chat">
    <i data-lucide="message-circle" class="chat-icon" stroke-width="2"></i>
    <span class="notification-badge">5</span>
</button>

<div class="chat-popup" id="chatPopup">
    <div class="chat-header">
        <div class="chat-tabs">
            <button class="chat-tab chat-tab--active" data-tab="direct">Direct Messages</button>
            <button class="chat-tab" data-tab="support">Support Groups</button>
        </div>
        <button class="chat-close-btn" id="chatCloseBtn" aria-label="Close chat">
            <i data-lucide="x" class="close-icon" stroke-width="2"></i>
        </button>
    </div>

    <div class="chat-content">
        <div class="chat-search">
            <i data-lucide="search" class="chat-search-icon" stroke-width="2"></i>
            <input type="text" placeholder="Search" class="chat-search-input" />
        </div>

        <div class="chat-tab-content chat-tab-content--active" id="directTab">
            <div class="chat-list">
                <div class="chat-item">
                    <div class="chat-avatar">
                        <img src="/assets/img/avatar.png" alt="Ethan Carter" />
                        <span class="online-indicator"></span>
                    </div>
                    <div class="chat-info">
                        <h4 class="chat-name">Ethan Carter</h4>
                        <span class="chat-status">Online</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="chat-tab-content" id="supportTab">
            <div class="chat-list">
                <div class="chat-item">
                    <div class="chat-avatar group-avatar">
                        <img src="/assets/img/avatar.png" alt="Recovery Support" />
                    </div>
                    <div class="chat-info">
                        <h4 class="chat-name">Recovery Support</h4>
                        <span class="chat-status">24 members online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
