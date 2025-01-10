<h3>Customer Support Chat</h3>
<div id="chat-box"></div>

<form id="chat-form">
  <textarea id="message" placeholder="Type your message..." required></textarea>
  <button type="submit">Send</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function loadMessages() {
    $.get('fetch-chat.php', function(data) {
      $('#chat-box').html(data);
    });
  }

  // Load messages every 3 seconds
  setInterval(loadMessages, 3000);

  // Send message on form submit
  $('#chat-form').submit(function(e) {
    e.preventDefault();
    const message = $('#message').val();
    $.post('send-message.php', { message: message }, function() {
      $('#message').val('');  // Clear the message input
      loadMessages();
    });
  });

  // Initial load
  loadMessages();
</script>
