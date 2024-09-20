<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- JavaScript -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <!-- End JavaScript -->

    <!-- CSS -->
    <link rel="stylesheet" href="/style.css">
    <!-- End CSS -->

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- End Font Awesome -->

    <!-- Custom Scrollbar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <!-- End Custom Scrollbar -->

    <title>Chat Pusher</title>
</head>
<body>

    <div class="chat">
        <div class="chat-title">
            <h1>John Doe</h1>
            <h2 class="online-text">
                <i class="fas fa-circle online-icon"></i> Online
            </h2>
            <figure class="avatar">
                <img src="https://freesvg.org/img/abstract-user-flat-3.png" />
            </figure>
        </div>
        <div class="messages">
            <div class="messages-content"></div>
        </div>
        <form class="message-box">
            <textarea type="text" id="message" name="message" class="message-input" placeholder="Type message..."></textarea>
            <button type="submit" class="message-submit">Send</button>
        </form>
    </div>

</body>

<script>
    const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'eu'});
    const channel = pusher.subscribe('public');

    let $messages = $(".messages-content");
    let i = 0;

    let Fake = [
        "What's the weather like today?",
        "I'm feeling pretty good myself",
        "Have you ever tried making a game with JavaScript?",
        "I appreciate that! You're a great person too.",
        "I just have a good feeling about you",
        "I think you're very creative and resourceful",
        "Well, I'm off to explore some new codepen projects",
        "It was fun talking to you. Let's chat again soon",
        "I'm excited to see what you create next",
        "See you later!",
        "🙂"
    ];

    $(window).on('load', function () {
        $messages.mCustomScrollbar();
        setTimeout(fakeMessage, 100);
    });

    function updateScrollbar() {
        $messages.mCustomScrollbar("update").mCustomScrollbar("scrollTo", "bottom", {
            scrollInertia: 10,
            timeout: 0
        });
    }

    function fakeMessage() {
        if ($(".message-input").val() != "") {
            return false;
        }
        $('<div class="message loading new"><figure class="avatar"><img src="https://freesvg.org/img/abstract-user-flat-3.png" /></figure><span></span></div>').appendTo($(".mCSB_container"));
        setTimeout(function () {
            $(".message.loading").remove();
            $('<div class="message new"><figure class="avatar"><img src="https://freesvg.org/img/abstract-user-flat-3.png" /></figure>' + Fake[i] + "</div>").appendTo($(".mCSB_container")).addClass("new");
            updateScrollbar();
            i++;
        }, 1000 + Math.random() * 20 * 100);
    }

    channel.bind('chat', function (data) {
        $.post("/receive", {
            _token: '{{csrf_token()}}',
            message: data.message,
        }).done(function (res) {
            $(".messages .messages-content .mCSB_container > .message").last().after(res);
            $(document).scrollTop($(document).height());
        });
    });

    $("form").submit(function (event) {
        event.preventDefault();
        let message = $("form #message").val();
        if (message === "") {
            return;
        }

        $.ajax({
            url: "/broadcast",
            method: 'POST',
            headers: {
                'X-Socket-Id': pusher.connection.socket_id
            },
            data: {
                _token: '{{csrf_token()}}',
                message: message,
            }
        }).done(function (res) {
            $(".messages .messages-content .mCSB_container > .message").last().after(res);
            $("form #message").val(null);
            updateScrollbar();
            setTimeout(fakeMessage, 1000 + Math.random() * 20 * 100);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            let errorMessage = `Error: ${textStatus} - ${errorThrown}`;
            alert(errorMessage);
            console.log(jqXHR.responseText);
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

</html>
