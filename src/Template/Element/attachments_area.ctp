<div class="attachments-container">
    <?= $this->element('Attachments.attachments_list', [
        'options' => $options,
        'entity' => $entity
    ]); ?>
    <?php if ($options['full_mode'] === true) : ?>
        <?= $this->element('Attachments.attachments_dropzone', [
            'options' => $options,
            'entity' => $entity
        ]); ?>
    <?php endif; ?>
</div>