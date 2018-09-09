<div class="border border-success mb-2">
    <div class="categoryBlockHeader">
        <p class="h5">Ключевые слова</p>
    </div>

    <div class="categoryBlockContent">
        <?php foreach ($keywords as $value) { ?>
            <a class="text-danger" href="contentview.php?keywordsearch=<?= $value ?>"><?= $value ?></a>
        <?php } ?>
    </div>
</div>