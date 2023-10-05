<?php

return [

    'cleanup' => [
       

        'default_strategy' => [

            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => 7,

            /*
             * The number of days for which daily backups must be kept.
             */
            //'keep_daily_backups_for_days' => 16,
            'keep_daily_backups_for_days' => 3,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 0,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 4,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 2,

            /*
             * After cleaning up the backups remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];