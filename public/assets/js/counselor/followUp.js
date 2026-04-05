document.addEventListener("DOMContentLoaded", function () {
  if (!window.NewPathFollowupThread) {
    return;
  }

  window.NewPathFollowupThread.initPopup({
    fallbackName: "Client",
    emptyMessage: "No messages yet.",
    composePlaceholder: "Type a message...",
    loadMessagesUrl: function (sessionId) {
      return "/counselor/sessions?ajax=get_messages&session_id=" + sessionId;
    },
    sendMessageUrl: function () {
      return "/counselor/sessions?ajax=send_message";
    },
  });
});
