<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flea Market</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/trades.css') }}">
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" style="height: 36px;">
            </a>
        </div>
    </header>

    <main>
        <div class="trade">
            <div class="trade-sidebar">
                <div class="trade-sidebar__title">その他の取引</div>
                <div class="trade-sidebar__list">
                    @foreach($otherPurchases as $other)
                        @if($other->trade && $other->status === 'trading')
                            <div class="trade-sidebar__item">
                                <a href="{{ route('trades.show', $other->id) }}" class="trade-sidebar__link">
                                    {{ $other->item->name }}
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="trade-container">
                <div class="trade-header">
                    <div class="trade-header__profile">
                        <div class="trade__profile-avatar">
                            @if($partner && $partner->profile_image)
                                <img src="{{ asset('storage/' . $partner->profile_image) }}" 
                                    alt="{{ $partner->username }}" 
                                    class="avatar-img">
                            @else
                                <img src="{{ asset('images/default-avatar.png') }}" 
                                    alt="" 
                                    class="avatar-img">
                            @endif
                        </div>
                        <div class="trade__profile-name">{{ $partner && $partner->username ? $partner->username : '不明なユーザー' }}さんとの取引画面</div>
                    </div>
                    @php
                        $loginUserId = auth()->id();
                    @endphp

                    @if($purchase->user_id === $loginUserId && $purchase->status === 'trading')
                        <button class="end-trade-btn" id="openRatingModal">取引を完了する</button>
                    @endif
                </div>

                <div class="trade-product">
                    <img src="{{ asset('storage/' . $purchase->item->image_path) }}" alt="{{ $purchase->item->username }}">
                    <div class="trade-product-info">
                        <div class="trade-product__name">{{ $purchase->item->name }}</div>
                        <div class="trade-product__price">¥{{ number_format($purchase->item->price) }}</div>
                    </div>
                </div>

                <div class="trade-chat" id="tradeChat">
                    @foreach($messages as $msg)
                        @if($msg->user_id === auth()->id())
                            <div class="trade-message right"  data-id="{{ $msg->id }}">
                                <div class="trade-chat__body">
                                    <div class="trade-chat__name right">{{ auth()->user()->username }}</div>

                                    <div class="trade-chat__border">
                                        @if($msg->message)
                                            <div class="message-text">{{ $msg->message }}</div>
                                        @endif
                                        @if($msg->image_path)
                                            <img src="{{ asset('storage/' . $msg->image_path) }}" class="chat-image">
                                        @endif
                                    </div>
                                    <div class="message-actions">
                                        <button class="edit-btn">編集</button>
                                        <button class="delete-btn">削除</button>
                                    </div>
                                </div>
                                <div class="trade-chat__avatar">
                                    <img src="{{ $msg->user->profile_image ? asset('storage/' . $msg->user->profile_image) : asset('images/default-avatar.png') }}" alt="" class="chat-avatar-img">
                                </div>
                            </div>
                        @else
                            <div class="trade-message left">
                                <div class="trade-chat__avatar">
                                    <img src="{{ $msg->user->profile_image ? asset('storage/' . $msg->user->profile_image) : asset('images/default-avatar.png') }}" alt="" class="chat-avatar-img">
                                </div>
                                <div class="trade-chat__body">
                                    <div class="trade-chat__name left">{{ $msg->user->username }}</div>
                                    <div class="trade-chat__border">
                                        @if($msg->message)
                                            <div class="message-text">{{ $msg->message }}</div>
                                        @endif
                                        @if($msg->image_path)
                                            <img src="{{ asset('storage/' . $msg->image_path) }}" class="chat-image">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <form action="{{ route('trade.messages.store', $purchase->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="error-messages">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="trade-input-area">
                        <input type="text" name="message" id="chatInput" data-trade-id="{{ $trade->id }}" placeholder="取引メッセージを入力してください">
                        <button type="button" class="trade__image-button"id="imageButton">画像を追加</button>
                        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;">
                        <button type="submit" class="trade__send-button" id="sendButton">
                            <img src="{{ asset('images/send.jpg') }}" alt="送信" class="send-icon">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    {{-- 購入者の評価モーダル --}}
    <div id="buyerRatingModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-content__title">取引が完了しました。</div>
            <form method="POST" action="{{ route('purchases.rate', $purchase->id) }}">
                @csrf
                <div class="modal-content__text">今回の取引相手はどうでしたか？</div>
                <div class="stars">
                    @for($i=1;$i<=5;$i++)
                        <label>
                            <input type="radio" name="rating" value="{{ $i }}">
                            <span class="star">&#9733;</span>
                        </label>
                    @endfor
                </div>
                <div class="modal-buttons">
                    <button type="submit">送信する</button>
                </div>
            </form>
        </div>
    </div>
    {{-- 出品者の評価モーダル --}}
    @if($purchase->item->user_id === $loginUserId && $purchase->status === 'buyer_completed' && is_null($purchase->buyer_rating))
        <div id="sellerRatingModal" class="modal">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-content__title">取引が完了しました。</div>
                <form method="POST" action="{{ route('purchases.rate', $purchase->id) }}">
                    @csrf
                    <div class="modal-content__text">今回の取引相手はどうでしたか？</div>
                    <div class="stars">
                        @for($i=1;$i<=5;$i++)
                            <label>
                                <input type="radio" name="rating" value="{{ $i }}">
                                <span class="star">&#9733;</span>
                            </label>
                        @endfor
                    </div>
                    <div class="modal-buttons">
                        <button type="submit">送信する</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
    const chat = document.getElementById("tradeChat");
    const input = document.getElementById("chatInput");
    const sendButton = document.getElementById("sendButton");
    const purchaseId = "{{ $purchase->id }}";
    const myUserId = parseInt("{{ auth()->id() }}");
    const imageButton = document.getElementById("imageButton");
    const imageInput = document.getElementById("imageInput");
    let selectedFile = null;

    imageButton.addEventListener("click", () => {
        imageInput.click();
    });

    imageInput.addEventListener("change", (e) => {
        if(e.target.files.length === 0) return;

        selectedFile = e.target.files[0];
        sendImage(); // 画像だけ送信
    });

    function addMessage(data, isMine){
        const msg = document.createElement("div");
        msg.classList.add("trade-message", isMine ? "right" : "left");
        msg.dataset.id = data.id;

        const avatarUrl = data.user.profile_image
            ? `/storage/${data.user.profile_image}`
            : '/images/default-avatar.png';

        let contentHtml = '<div class="trade-chat__border">';
        if (data.message) {
            contentHtml += `<div class="message-text">${data.message}</div>`;
        }
        if (data.image_path) {
            contentHtml += `<img src="/storage/${data.image_path}" class="chat-image">`;
        }
        contentHtml += '</div>';

        msg.innerHTML = `
            <div class="trade-chat__body">
                <div class="trade-chat__name ${isMine ? 'right' : 'left'}">${data.user.username}</div>
                ${contentHtml}
                ${isMine ? `
                <div class="message-actions">
                    <button class="edit-btn">編集</button>
                    <button class="delete-btn">削除</button>
                </div>` : ''}
            </div>
            <div class="trade-chat__avatar">
                <img src="${avatarUrl}" alt="" class="chat-avatar-img">
            </div>
        `;
        chat.appendChild(msg);
        chat.scrollTop = chat.scrollHeight;
    }

    // 編集・削除処理
    document.addEventListener("click", function(e){
        if(e.target.classList.contains("edit-btn")){
            const msgDiv = e.target.closest(".trade-message");
            const msgId = msgDiv.dataset.id;
            const textEl = msgDiv.querySelector(".message-text");
            const oldText = textEl.textContent;

            textEl.innerHTML = `<input type="text" class="edit-input" value="${oldText}">`;
            const inputEl = textEl.querySelector(".edit-input");
            inputEl.focus();

            inputEl.addEventListener("keydown", function(ev){
                if(ev.key === "Enter"){
                    ev.preventDefault();
                    fetch(`/messages/${msgId}`, {
                        method: "PUT",
                        headers:{
                            "Content-Type":"application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message: inputEl.value })
                    })
                    .then(res => res.json())
                    .then(data => textEl.textContent = data.message)
                    .catch(err => console.error("更新エラー", err));
                }
                if(ev.key === "Escape"){
                    textEl.textContent = oldText;
                }
            });
        }

        if(e.target.classList.contains("delete-btn")){
            if(!confirm("このメッセージを削除しますか？")) return;
            const msgDiv = e.target.closest(".trade-message");
            const msgId = msgDiv.dataset.id;

            fetch(`/messages/${msgId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => { if(res.ok) msgDiv.remove(); else alert("削除に失敗しました"); })
            .catch(err => console.error("削除エラー", err));
        }
    });

    function sendImage() {
        const formData = new FormData();
        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
        formData.append("image", selectedFile);

        fetch(`/trades/${purchaseId}/messages`, {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            addMessage(data, data.user_id === myUserId);
            selectedFile = null;
            imageInput.value = ""; // 選択リセット
        })
        .catch(err => console.error("画像送信エラー:", err));
    }

    sendButton.addEventListener("click", sendMessage);

    function sendMessage() {
        const text = input.value.trim();
        if(!text && !selectedFile) return;

        const formData = new FormData();
        formData.append("_token", document.querySelector('meta[name="csrf-token"]').content);
        formData.append("message", text);
        if(selectedFile) formData.append("image", selectedFile);

        fetch(`/trades/${purchaseId}/messages`, {
            method: "POST",
            body: formData
        })
        .then(res => {
            if(!res.ok) return res.json().then(data => { throw data; });
            return res.json();
        })
        .then(data => {
            addMessage(data, data.user_id === myUserId);
            input.value = "";
            selectedFile = null;
            imageInput.value = "";
            document.querySelector('.error-messages')?.remove();
        })
        .catch(async (err) => {
            document.querySelector('.error-messages')?.remove();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-messages';
            errorDiv.style.color = 'red';

            if(err.errors){
                Object.values(err.errors).forEach(arr => {
                    arr.forEach(msg => {
                        const p = document.createElement('p');
                        p.textContent = msg;
                        errorDiv.appendChild(p);
                    });
                });
            }
            else if(err.message){
                const p = document.createElement('p');
                p.textContent = err.message;
                errorDiv.appendChild(p);
            }
            else {
                const p = document.createElement('p');
                p.textContent = '送信中にエラーが発生しました';
                errorDiv.appendChild(p);
            }

            document.querySelector('.trade-input-area').prepend(errorDiv);
        });

    }

    document.addEventListener('DOMContentLoaded', function(){
        const openBtn = document.getElementById('openRatingModal');
        const modal = document.getElementById('ratingModal');
        const stars = modal?.querySelectorAll('.star');
        const overlay = document.querySelector('.modal-overlay');
        const modals = document.querySelectorAll('.modal');

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                const buyerModal = document.getElementById('buyerRatingModal');
                if (buyerModal) buyerModal.classList.remove('hidden');
            });
        }

        modals.forEach(modal => {
            const stars = modal.querySelectorAll('.star');
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.classList.add('selected');
                            s.classList.remove('text-gray-300');
                        } else {
                            s.classList.remove('selected');
                            s.classList.add('text-gray-300');
                        }
                    });
                    modal.querySelector(`input[name="rating"][value="${index + 1}"]`).checked = true;
                });
            });

            modal.querySelector('.modal-overlay')?.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });
    });

    document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("chatInput");
        if (!input) return;

        const tradeId = input.dataset.tradeId;
        const storageKey = `chat_draft_${tradeId}`;

        // ページ読み込み時に復元
        const savedDraft = sessionStorage.getItem(storageKey);
        if (savedDraft) {
            input.value = savedDraft;
        }

        // 入力のたびに保存
        input.addEventListener("input", () => {
            sessionStorage.setItem(storageKey, input.value);
        });

        // 送信したら下書きを削除
        const sendBtn = document.getElementById("sendButton");
        if (sendBtn) {
            sendBtn.addEventListener("click", () => {
                sessionStorage.removeItem(storageKey);
            });
        }
    });


</script>

</body>
</html>