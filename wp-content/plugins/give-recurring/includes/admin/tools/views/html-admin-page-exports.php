<?php

/**
 * This function adds a new table row for
 * Exporting Subscriptions.
 *
 * The output is visible on the -
 * Donations > Tools > Export page
 *
 * @since 1.6
 *
 * @return void
 */
function give_recurring_add_export_subscriptions_row() {
?>
	<tr class="give-export-subscriptions">
		<td scope="row" class="row-title">
			<h3>
				<span><?php esc_html_e( 'Export Upcoming Subscriptions Renewals', 'give-recurring' ); ?></span>
			</h3>
			<p><?php esc_html_e( 'Download a CSV of upcoming subscriptions renewals.', 'give-recurring' ); ?></p>
		</td>
		<td>
			<div class="give-sr-wrap">
				<form method="post">
					<?php
						// Dropdown to display forms.
						printf(
							"<div class='give-sr-export'>%s</div>",
							Give()->html->forms_dropdown( array(
								'name'        => 'subscription_renewal_per_form',
								'id'          => 'subscription_renewal_per_form',
								'chosen'      => true,
								'placeholder' => __( 'All recurring forms', 'give-recurring' ),
								'query_args'  => array(
									'meta_query' => array(
										array(
											'key'     => '_give_recurring',
											'value'   => array( 'no' ),
											'compare' => 'NOT IN'
										)
									)
								)
							) )
						);

						// Field to select the start date.
						echo Give()->html->date_field( array(
							'id'          => 'give_renewal_subscriptions_start_date',
							'name'        => 'give_renewal_subscriptions_start_date',
							'placeholder' => esc_attr__( 'Start date', 'give-recurring' ),
						) );

						// Field to select the end date.
						echo Give()->html->date_field( array(
							'id'          => 'give_renewal_subscriptions_end_date',
							'name'        => 'give_renewal_subscriptions_end_date',
							'placeholder' => esc_attr__( 'End date', 'give-recurring' ),
						) );

						$date_format = get_option( 'date_format' );
						printf(
							'<p class="give-field-description"><i>%1$s <time>%2$s</time> and <time>%3$s</time> %4$s</i></p>',
							__( 'If the date parameters are not set, then the upcoming renewals between the period', 'give-recurring' ),
							date( $date_format, current_time( 'timestamp' ) ),
							date( $date_format, strtotime( current_time( 'mysql' ) . ' +1 month' ) ),
							__( 'will be fetched.', 'give-recurring' )
						);
					?>
					<input type="hidden" name="give-action"
					       value="subscriptions_renewal_export"/>
					<input type="submit"
					       value="<?php esc_attr_e( 'Generate CSV', 'give-recurring' ); ?>"
					       class="button-secondary"/>
				</form>
			</div>
		</td>
	</tr>
<?php
}

add_action( 'give_tools_tab_export_table_bottom', 'give_recurring_add_export_subscriptions_row' );

/**
 * Return only recurring forms for subscription export form dropdown
 * Note: only for internal logic
 *
 * @since 1.7
 *
 * @param $args
 *
 * @return array
 */
function __give_recurring_give_ajax_form_search_args( $args ) {
	if ( ! empty( $_POST['fields'] ) ) {
		$_post = array_map( 'give_clean', wp_parse_args( $_POST['fields'] ) );

		if (
			array_key_exists( 'give-action', $_post )
			&& 'subscriptions_renewal_export' === $_post['give-action']
		) {
			$args['meta_query'] = array(
				array(
					'key'     => '_give_recurring',
					'value'   => array( 'no' ),
					'compare' => 'NOT IN'
				)
			);
		}
	}

	return $args;
}

add_action( 'give_ajax_form_search_args', '__give_recurring_give_ajax_form_search_args' );


