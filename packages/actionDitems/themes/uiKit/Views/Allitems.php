<?php

namespace packages\actionDitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\themes\uiKit\Components\Components as Components;
use packages\actionDitems\themes\uiKit\Models\Model as ArticleModel;

class Allitems extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    /**
     * @var ArticleModel
     */
    public $model;

    /**
     * Main view entrypoint
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $reminders = $this->getData('reminders', 'array');

        $this->model->setBackgroundColor('#ffffff');

        if (empty($reminders) || is_null($reminders)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_reminders_yet#}', array('style' => 'mit_no_items'));
        }

        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'show_visit_date',
            'visibility' => 'hidden'
        ));
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'show_visit_time',
            'visibility' => 'hidden'
        ));
        $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
            'variable' => 'reminder_name',
            'visibility' => 'hidden'
        ));

        $layout = new \stdClass();
        $layout->top = 80;
        $layout->bottom = 0;
        $layout->left = 0;
        $layout->right = 0;

        foreach ($reminders as $reminder) {

            $styles = [];
            $description = [];

            $item = $reminder->item;

            $name = ($reminder->name ? $reminder->name : $item->name);
            $type = $item->type;

            if (empty($name)) {
                continue;
            }

            switch ($type) {
                case 'routine':

                    $params['date_icon'] = 'uikit-icon-routines-v2.png';

                    if (isset($reminder->date)) {

                        $stamp = $reminder->date;
                        $date = date('d M Y, H:i', $stamp);
                        $end_date = date('d M Y', $reminder->end_date);

                        $date_label = ($stamp > time() ? '{#starting#}' : '{#started#}');

                        $pattern = $this->model->getEventPattern($reminder);

                        if ($pattern == 'monthly') {
                            $name .= ' - Monthly event';
                            $description[] = $date_label . ' at ' . $date . ' / Ends ' . $end_date;

                            if (isset($reminder->pattern->day_of_month) AND $day = $reminder->pattern->day_of_month) {
                                $separation_count = $reminder->pattern->separation_count;
                                $description[] = 'Recurs ' . $this->model->getRecurringSeparation($separation_count) . ' month on ' . $this->model->getRecurringMonths($day);
                                $description[] = $this->model->getFirstMontlyOccurrence($item);
                            }

                        } else if ($pattern == 'weekly') {

                            $name .= ' - Weekly event';
                            $description[] = $date_label . ' at ' . $date . ' / Ends ' . $end_date;

                            if (isset($reminder->pattern->day_of_week) AND $days_data = $reminder->pattern->day_of_week) {
                                $separation_count = $reminder->pattern->separation_count;
                                $description[] = 'Recurs ' . $this->model->getRecurringSeparation($separation_count) . ' ' . $this->model->getRecurringDays($days_data);
                                $description[] = $this->model->getFirstWeeklyOccurrence($item);
                            }

                        } else {
                            $description[] = 'Starting at ' . date('H:i', $stamp);
                            $description[] = date('d M Y', $stamp);
                        }

                    }

                    break;

                case 'visit':

                    $params['date_icon'] = 'uikit-icon-search-visits-v2.png';
                    $name .= ' - Visit';
                    $description[] = 'Starting at ' . date('H:i', $reminder->date);
                    $description[] = date('d M Y', $reminder->date);

                    break;

                case 'note':

                    $params['date_icon'] = 'uikit-icon-search-notes.png';
                    $name .= ' - Note';
                    $description[] = 'Starting at ' . date('H:i', $reminder->date);
                    $description[] = date('d M Y', $reminder->date);

                    break;
            }

            $params['id'] = 'row_' . $reminder['id'];

            if ($type != 'routine') {
                if (time() > $reminder->date) {
                    $styles['color'] = '#D40511';
                }
            }

            $params['swipe_right'] = array_merge(
                $this->getDeleteButton($reminder),
                $this->getUpdateButton($reminder)
            );

            $params['onclick'] = array(
                $this->getOnclickShowDiv('show_reminder', array(
                    'background' => 'blur',
                    'tap_to_close' => 1,
                    'transition' => 'from-bottom',
                    'layout' => $layout
                )),
                $this->getOnclickSetVariables(array(
                    'reminder_id' => $reminder->id,
                    'show_visit_date' => date('d M Y', $reminder->next_date),
                    'show_visit_time' => date('H:i', $reminder->next_date),
                    'reminder_name' => $name,
                    'reminder_message' => ($reminder->message ? $reminder->message : $item->description)
                ))
            );

            $this->layout->scroll[] = $this->components->uiKitListitem($name, $description, $params, $styles);

            $this->layout->scroll[] = $this->getComponentDivider(array(
                'style' => 'article-uikit-divider-thin'
            ));

            unset($params['swipe_right']);
            unset($styles);
        }

        return $this->layout;
    }

    public function getDivs()
    {

        $divs['show_reminder'] = $this->components->getShowReminderDiv(array(
            'title' => 'Reminder',
            'subtitle' => ''
        ));

        return $divs;
    }

    private function getDeleteButton($reminder)
    {
        return [
            $this->uiKitSwipeDeleteButton(array(
                'identifier' => $reminder['id'],
                'delete_path' => 'Createvisit/deleteReminder/' . $reminder['id']
            ))
        ];
    }

    private function getUpdateButton($reminder)
    {
        if ($reminder->recurring) {
            return [];
        }

        return [
            $this->uiKitSwipeUpdateButton(array(
                'identifier' => $reminder['id'],
            ))
        ];
    }

}