<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant LLM RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Assistant LLM pour Ressources Humaines</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="chat-box" style="max-width:600px;margin:auto; min-height:400px; margin-bottom:120px;">
            <?php if (!empty($chatHistory) && is_array($chatHistory)): ?>
                <?php foreach ($chatHistory as $item): ?>
                    <?php if (!empty($item['question'])): ?>
                        <div class="d-flex justify-content-end mb-2">
                            <div class="chat-bubble bg-primary text-white p-3 rounded" style="max-width:80%;">
                                <strong>Vous :</strong><br>
                                <?php echo nl2br(htmlspecialchars($item['question'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($item['response'])): ?>
                        <div class="d-flex justify-content-start mb-2">
                            <div class="chat-bubble bg-light border p-3 rounded" style="max-width:80%;">
                                <strong>Assistant :</strong><br>
                                <?php
                                if (is_array($item['response'])) {
                                    echo '<pre>' . htmlspecialchars(print_r($item['response'], true)) . '</pre>';
                                } else {
                                    echo nl2br(htmlspecialchars($item['response']));
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?= Flight::base() ?>/llm/ask" class="chat-input position-fixed bottom-0 start-50 translate-middle-x w-100" style="max-width:600px; background:#fff; padding:16px 0; box-shadow:0 -2px 8px rgba(0,0,0,0.05);">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-10">
                        <textarea class="form-control" id="question" name="question" rows="2" placeholder="Votre question..." required><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                    </div>
                    <div class="col-2 text-end">
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onload = function() {
            var chatBox = document.querySelector('.chat-box');
            var bubbles = chatBox ? chatBox.querySelectorAll('.chat-bubble') : [];
            if (bubbles.length > 0) {
                bubbles[bubbles.length - 1].scrollIntoView({ behavior: 'auto', block: 'end' });
            }
        };
    </script>
</body>
</html>