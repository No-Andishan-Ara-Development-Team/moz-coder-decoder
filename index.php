<?php
// لیست کلمات بد (به صورت کوچک) - هر کلمه با کاما جدا شده
$badWords = ['کس‌کش', 'گوه‌خور', 'کونی', 'هرزه', 'جنده', 'بی‌شرف', 'حروم‌زاده', 'کونی‌مونی', 'گوه‌بخور', 'مرده‌خور', 'بی‌عرضه', 'دیوونه'];
 

function censorBadWords($text, $badWords) {
    // جدا کردن متن به کلمات (با space و علائم)
    $pattern = '/\b(' . implode('|', array_map('preg_quote', $badWords)) . ')\b/ui';
    // جایگزینی کلمات بد با ستاره (هم‌اندازه کلمه)
    $callback = function($matches) {
        return str_repeat('*', mb_strlen($matches[0]));
    };
    return preg_replace_callback($pattern, $callback, $text);
}

function encodeText($text) {
    $map = ['0' => 'm', '1' => 'o', '2' => 'z', '3' => 'mo', '4' => 'oo', '5' => 'zz', '6' => 'mz', '7' => 'zm', '8' => 'oz', '9' => 'zo'];
    $encoded = '';
    foreach (str_split($text) as $char) {
        $ascii = strval(ord($char));
        foreach (str_split($ascii) as $digit) {
            $encoded .= $map[$digit] . '-';
        }
        $encoded .= '|';
    }
    return rtrim($encoded, '|');
}

function decodeText($code) {
    $revMap = ['m' => '0', 'o' => '1', 'z' => '2', 'mo' => '3', 'oo' => '4', 'zz' => '5', 'mz' => '6', 'zm' => '7', 'oz' => '8', 'zo' => '9'];
    $chars = explode('|', $code);
    $decoded = '';
    foreach ($chars as $charBlock) {
        $digits = explode('-', rtrim($charBlock, '-'));
        $ascii = '';
        foreach ($digits as $symbol) {
            if (isset($revMap[$symbol])) {
                $ascii .= $revMap[$symbol];
            } else {
                return false;
            }
        }
        $decoded .= chr((int)$ascii);
    }
    return $decoded;
}

if (isset($_GET['d'])) {
    $decoded = decodeText($_GET['d']);
    echo "<!DOCTYPE html><html lang='fa'><head><meta charset='UTF-8'><title>نتیجه</title>
    <style>
        @font-face {
            font-family: 'CustomFont';
            src: url('font/1.ttf') format('truetype');
        }
        body {
            background: #111;
            color: #eee;
            font-family: 'CustomFont';
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
            padding: 20px;
        }
        .result-box {
            background: #1e1e1e;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,255,255,0.2);
            max-width: 600px;
            width: 100%;
        }
        p {
            font-size: 18px;
            word-wrap: break-word;
            direction: rtl;
            text-align: right;
        }
    </style></head><body>";

    if ($decoded === false) {
        echo "<div class='result-box'><p>❌ لینک نامعتبر است.</p></div>";
    } elseif (filter_var($decoded, FILTER_VALIDATE_URL)) {
        header("Location: $decoded");
        exit;
    } else {
        echo "<div class='result-box'><h2>🔓 متن رمزگشایی‌شده:</h2><p>" . htmlspecialchars($decoded) . "</p></div>";
    }
    echo "</body></html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>رمزگذار Moz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @font-face {
            font-family: 'CustomFont';
            src: url('font/1.ttf') format('truetype');
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'CustomFont';
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-height: 100vh;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #00f2fe, #4facfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }
        form {
            background: rgba(255,255,255,0.05);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        textarea {
            width: 100%;
            min-height: 120px;
            padding: 15px;
            font-size: 18px;
            border: none;
            border-radius: 10px;
            resize: vertical;
            margin-bottom: 20px;
            background: #222;
            color: #fff;
        }
        button {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: #111;
            font-size: 18px;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        button:hover {
            box-shadow: 0 0 15px #00f2fe;
            transform: scale(1.05);
        }
        .link-box {
            margin-top: 30px;
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0,255,255,0.3);
            width: 100%;
            max-width: 600px;
            word-break: break-word;
            text-align: center;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            background: #333;
            color: #0ff;
            text-align: center;
        }
        input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 10px #00f2fe;
        }
        .copy-btn {
            margin-top: 15px;
            background-color: #00f2fe;
            color: #000;
            border: none;
            padding: 10px 25px;
            font-size: 14px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .copy-btn:hover {
            background-color: #4facfe;
        }

        @media (max-width: 600px) {
            h1 { font-size: 1.5rem; }
            textarea, input[type="text"] { font-size: 16px; }
            button, .copy-btn { font-size: 16px; padding: 10px 20px; }
        }
    </style>
</head>
<body>
    <img src="icon/MOZ.svg" alt="لوگو MOZ" style="width:80px; height:auto; margin-bottom: 20px;">
    <h1>🔐 رمزگذار Moz</h1>
    <form method="POST">
        <textarea name="text" placeholder="متن یا لینک را وارد کن..."></textarea><br>
        <button type="submit">رمزگذاری و ساخت لینک</button>
    </form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['text'])) {
    // فیلتر کردن کلمات بد
    $filtered = censorBadWords(trim($_POST['text']), $badWords);
    $encoded = encodeText($filtered);
    $link = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?d=' . urlencode($encoded);
    echo "
    <div class='link-box'>
        <strong>📎 لینک آماده:</strong><br>
        <input type='text' id='linkOutput' value='$link' readonly onclick='this.select()'>
        <button class='copy-btn' type='button' onclick='copyLinkFallback()'>📋 کپی لینک</button>
    </div>";
}
?>

<script>
function copyLinkFallback() {
    const input = document.getElementById('linkOutput');
    input.select();
    input.setSelectionRange(0, 99999); // موبایل
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            alert('✅ لینک با موفقیت کپی شد!');
        } else {
            alert('❌ کپی نشد. لطفاً دستی کپی کن.');
        }
    } catch (err) {
        alert('❌ مرورگر شما از کپی خودکار پشتیبانی نمی‌کند.');
    }
}
</script>
</body>
<footer>
<center>
<p>طراحی توسط تیم توسعه نو اندیشان آرا</p>
</center>
</footer>
</html>
