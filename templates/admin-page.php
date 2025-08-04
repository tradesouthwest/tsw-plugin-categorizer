<div class="wrap">
    <h1><?php esc_html_e( 'Plugin Categorizer', 'tpc-plugin-categorizer' ); ?></h1>
    <p><?php esc_html_e( 'Categorize your installed plugins for better organization and management.', 'tpc-plugin-categorizer' ); ?></p>

    <div id="tpc-message" class="notice notice-success" style="display: none;">
        <p></p>
    </div>

    <div class="tpc-container">
        <!-- Section for Uncategorized Plugins -->
        <div class="tpc-uncategorized-section">
            <h2><?php esc_html_e( 'Uncategorized Plugins', 'tpc-plugin-categorizer' ); ?></h2>
            <div class="tpc-plugin-list">
                <?php
                if ( ! empty( $uncategorized_plugins ) ) {
                    foreach ( $uncategorized_plugins as $plugin_slug => $plugin_data ) {
                        $suggestion = isset( $suggested_categories[ $plugin_slug ] ) ? $suggested_categories[ $plugin_slug ] : '';
                        ?>
                        <div class="tpc-plugin-item" data-plugin-slug="<?php echo esc_attr( $plugin_slug ); ?>">
                            <div class="tpc-plugin-header">
                                <h3><?php echo esc_html( $plugin_data['Name'] ); ?></h3>
                                <p><?php echo esc_html( $plugin_data['Description'] ); ?></p>
                            </div>
                            <div class="tpc-plugin-actions">
                                <select class="tpc-category-select">
                                    <option value=""><?php esc_html_e( 'Select Category', 'tpc-plugin-categorizer' ); ?></option>
                                    <?php foreach ( $this->categories as $category ) : ?>
                                        <option value="<?php echo esc_attr( $category ); ?>" <?php selected( $suggestion, $category ); ?>>
                                            <?php echo esc_html( $category ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="button button-primary tpc-save-button" type="button"><?php esc_html_e( 'Save', 'tpc-plugin-categorizer' ); ?></button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p>' . esc_html__( 'All plugins are categorized!', 'tpc-plugin-categorizer' ) . '</p>';
                }
                ?>
            </div>
        </div>

        <!-- Section for Categorized Plugins -->
        <div class="tpc-categorized-section">
            <h2><?php esc_html_e( 'Categorized Plugins', 'tpc-plugin-categorizer' ); ?></h2>
            <div class="tpc-categorized-list">
                <?php
                $categorized_by_group = array();
                foreach ( $categorized_plugins as $plugin_slug => $category ) {
                    $plugin_data = $all_plugins[ $plugin_slug ];
                    if ( ! isset( $categorized_by_group[ $category ] ) ) {
                        $categorized_by_group[ $category ] = array();
                    }
                    $categorized_by_group[ $category ][ $plugin_slug ] = $plugin_data;
                }

                if ( ! empty( $categorized_by_group ) ) {
                    foreach ( $this->categories as $category ) {
                        if ( isset( $categorized_by_group[ $category ] ) ) {
                            echo '<h3>' . esc_html( $category ) . '</h3>';
                            echo '<ul class="tpc-categorized-group">';
                            foreach ( $categorized_by_group[ $category ] as $plugin_slug => $plugin_data ) {
                                ?>
                                <li class="tpc-categorized-item" data-plugin-slug="<?php echo esc_attr( $plugin_slug ); ?>">
                                    <strong><?php echo esc_html( $plugin_data['Name'] ); ?></strong>
                                    <button class="button button-secondary tpc-remove-button" type="button">
                                        <?php esc_html_e( 'Remove', 'tpc-plugin-categorizer' ); ?>
                                    </button>
                                </li>
                                <?php
                            }
                            echo '</ul>';
                        }
                    }
                } else {
                    echo '<p>' . esc_html__( 'No plugins have been categorized yet.', 'tpc-plugin-categorizer' ) . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
