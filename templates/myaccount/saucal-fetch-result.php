<h4>
    <?php
    _e( 'Result', 'sc-api-integration' ); ?>
</h4>
<div class="saucal-api-result-placeholder">
    <?php
    if ( ! empty( $response ) ): ?>
        <ul>
            <?php
            foreach ( $response as $title => $value ): ?>
                <li><strong><?php
                        echo $title; ?> :</strong><?php
                    echo $value; ?></li>
            <?php
            endforeach; ?>
        </ul>
    <?php
    endif; ?>
</div>