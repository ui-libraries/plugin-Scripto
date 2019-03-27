<h2><?php echo __('Transcribe This Item'); ?></h2>
<ol>
    <?php foreach ($doc->getPages() as $pageId => $pageName): ?>
    <li><a href="<?php echo url(array(
            'action' => 'transcribe',
            'item-id' => $item->id,
            'file-id' => $pageId,
        ),
        'scripto_action_item_file'); ?>" class="scripto-transcribe-item"><?php echo $pageName; ?></a></li>
    <?php endforeach; ?>
</ol>
