<?php

namespace packages\actionMcalendar\Models;

Trait TestData {


    public function getHourSelectorData(){
        return '5;5am;6;6am;7;7am;8;8am;9;9am;10;10am;11;11am;12;12:00;13;1pm;14;2pm;15;3pm;16;4pm;17;5pm;18;6pm;19;7pm;20;8pm;21;9pm;22;10pm;23;11pm';
    }

    public function getHours(){
        return [
            ['hour' => 5, 'time' => 'am'],
            ['hour' => 6, 'time' => 'am'],
            ['hour' => 7, 'time' => 'am'],
            ['hour' => 8, 'time' => 'am'],
            ['hour' => 9, 'time' => 'am'],
            ['hour' => 10, 'time' => 'am'],
            ['hour' => 11, 'time' => 'am'],
            ['hour' => 12, 'time' => 'pm'],
            ['hour' => 13, 'time' => 'pm'],
            ['hour' => 14, 'time' => 'pm'],
            ['hour' => 15, 'time' => 'pm'],
            ['hour' => 16, 'time' => 'pm'],
            ['hour' => 17, 'time' => 'pm'],
            ['hour' => 18, 'time' => 'pm'],
            ['hour' => 19, 'time' => 'pm'],
            ['hour' => 20, 'time' => 'pm'],
            ['hour' => 21, 'time' => 'pm'],
            ['hour' => 22, 'time' => 'pm'],
            ['hour' => 23, 'time' => 'pm'],
        ];
    }

    public function calendarTestData(){
        
        return [
            ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
            ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
            ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
            ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
            ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
        ];

    }

    public function calendarWeeklyTestData(){
        return [[
            'day' => '5','name' => 'mon','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '6','name' => 'tue','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '7','name' => 'wed','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '8','name' => 'thu','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '9','name' => 'fri','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '10','name' => 'sat','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],
            [
                'day' => '11','name' => 'sun','items' => [
                ['id' => 1,'type' => 'sleep', 'readablehour' => '6','length' => 1,'title' => 'Wake routine'],
                ['id' => 2,'type' => 'meditation', 'readablehour' => '6.5','length' => 1,'title' => 'Stretch and meditation'],
                ['id' => 3,'type' => 'training', 'readablehour' => '7.5','length' => 2,'title' => 'Training','subheader' => 'Power POWD'],
                ['id' => 4,'type' => 'food', 'readablehour' => '9.5','length' => 2,'title' => 'Breakfast','subheader' => 'Greens & Nut Bowl'],
                ['id' => 5,'type' => 'food', 'readablehour' => '13','length' => 2,'title' => 'Lunch', 'subheader' => 'Salmon and veggies'],
            ]],


        ];


    }


    
}