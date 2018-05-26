<?php if (is_array($entries) && count($entries)) : ?>
    <table class="widefat striped crgeary-jamstack-deployments-table">
        <thead>
            <tr>
                <th width="170">Date</th>
                <th width="100">Type</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry) :
                $color = array_key_exists($entry->type, $colors) ? $colors[$entry->type] : 'inherit';
            ?>
                <tr style="color: <?= esc_attr($color); ?>">
                    <td><?= $entry->time; ?></td>
                    <td><?= ucwords($entry->type); ?></td>
                    <td><?= $entry->message; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <style>
        .crgeary-jamstack-deployments-table tbody td, .crgeary-jamstack-deployments-table tbody th {
            color: inherit;
        }
    </style>
<?php else : ?>
    <p>There are no entries to view.</p>
<?php endif;