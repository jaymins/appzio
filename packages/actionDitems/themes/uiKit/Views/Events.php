<?php

namespace packages\actionDitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class Events extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1() {
        $this->layout = new \stdClass();

	    $this->layout->scroll[] = $this->getComponentRow(array(
		    $this->getComponentText(strtoupper('{#add_new_routine#}'), array(
			    'style' => 'add_item_button',
			    'onclick' => $this->getOnclickOpenAction('routineview',false,
				    array(
					    'id' => 'list-categories-home',
					    'sync_open' => 1,
					    'sync_close' => 1,
					    'back_button' => 1
				    ))
		    ))
	    ), array(), array(
		    'margin' => '15 0 0 0',
		    'text-align' => 'center'
	    ));

        $this->layout->scroll[] = $this->getComponentSpacer( 20 );
        
        $events = $this->getData('events', 'array');

        if ( empty($events) ) {
        	$this->layout->scroll[] = $this->getComponentText('{#you_don\'t_have_any_routines_added#}', array(), array(
	        	'color' => '#333333',
	        	'text-align' => 'center',
	        	'font-size' => '16',
	        	'margin' => '10 0 10 0',
	        ));

	        return $this->layout;
        }

        $params = array(
        	'date_icon' => 'uikit-icon-routines.png',
        );

	    foreach ( $events as $event ) {
		    $description = [];

		    $name = $event->name;

	    	if ( isset($event->reminders[0]->date) ) {

	    		$stamp = $event->reminders[0]->date;
			    $date = date('d M Y, H:i', $stamp);
			    $end_date = date('d M Y', $event->reminders[0]->end_date);

			    $date_label = ( $stamp > time() ? '{#starting#}' : '{#started#}' );

			    $pattern = $this->model->getEventPattern( $event->reminders[0] );

			    if ( $pattern == 'monthly' ) {
				    $name .= ' - Monthly event';
				    $description[] = $date_label . ' at ' . $date . ' / Ends ' . $end_date;

				    if ( isset($event->reminders[0]->pattern->day_of_month) AND $day = $event->reminders[0]->pattern->day_of_month ) {
					    $separation_count = $event->reminders[0]->pattern->separation_count;
					    $description[] = 'Recurs '. $this->model->getRecurringSeparation( $separation_count ) . ' month on ' . $this->model->getRecurringMonths( $day );
					    $description[] = $this->model->getFirstMontlyOccurrence( $event );
				    }

			    } else if ( $pattern == 'weekly' ) {
			    	
				    $name .= ' - Weekly event';
				    $description[] = $date_label . ' at ' . $date . ' / Ends ' . $end_date;

				    if ( isset($event->reminders[0]->pattern->day_of_week) AND $days_data = $event->reminders[0]->pattern->day_of_week ) {
				    	$separation_count = $event->reminders[0]->pattern->separation_count;
					    $description[] = 'Recurs '. $this->model->getRecurringSeparation( $separation_count ) . ' ' . $this->model->getRecurringDays( $days_data );
					    $description[] = $this->model->getFirstWeeklyOccurrence( $event );
				    }

			    } else {
                    $description[] = 'Starting at ' . date('H:i', $event->reminders[0]->date);
			    	$description[] = date('d M Y', $stamp);
			    }

		    }

		    $params['id'] = 'row_' . $event['id'];
		    $params['swipe_right'] = array(
			    $this->uiKitSwipeDeleteButton(array('identifier' => $event['id']))
		    );
		    $params['onclick'] = $this->getOnclickOpenAction('routineedit', false, array(
		       'id' => $event['id'],
		       'sync_open' => 1,
		       'back_button' => 1,
            ));

		    $this->layout->scroll[] = $this->components->uiKitListitem( $name, $description, $params );
		    
		    $this->layout->scroll[] = $this->getComponentDivider(array(
			    'style' => 'article-uikit-divider-thin'
		    ));
        }

        return $this->layout;
    }

}