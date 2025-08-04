<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Конструктор календаря</title>
    <style>
        @import url("https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800");
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:400,600,700");
        @import url("https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700");
        @import url("https://fonts.googleapis.com/css?family=Roboto:400,500,700");
        @import url("https://fonts.googleapis.com/css?family=Nunito:400,600,700");
        @import url("https://fonts.googleapis.com/css2?family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 0;
            size: landscape;
        }

        @media print {
            .noprint {
                display: none;
            }
        }

        html,
        body {
            background-color: #2C254A;
            margin: 0;
            padding: 0;
            font-family: 'poppins', sans-serif;
            justify-content: center;
            align-items: center;
        }

        .content {
            width: 297mm;
            height: 210mm;
            padding: 20px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .flex-container {
            display: flex;
            justify-content: space-evenly;
            align-items: stretch;
            flex-direction: row;
            flex-wrap: nowrap;
            background-color: #2C254A;
            height: 100%;
            padding: 0;
            gap: 5px;

        }

        .flex-container > div {
            background: #3b3363;
            border: 1px solid #473F72;
            border-radius: 5px;
            padding: 8px;
        }

        .flex-container .item2 {
            background-image: url({{ url('/images/tools/calendar/flag.jpg') }});
            background-size: cover;
            flex-grow: 3;
        }

        .flex-container .item1, .flex-container .item3 {
            width: 297px;
            flex-grow: 2;
        }

        .cwrap-container {
            display: flex;
            justify-content: space-evenly;
            align-items: stretch;
            flex-direction: row;
            flex-wrap: nowrap;
            height: 100%;
            padding: 0;

        }

        .cwrap-container > div {
            border: none;
        }

        .cwrap-container .cwrap-item2 {

        }

        .cwrap-container .cwrap-item1 {
            width: 165mm;
        }

        .month {
            margin-bottom: 10px;
            padding: 10px;
        }

        .month-name {
            color: #282828;
            font-weight: bold;
            text-align: center;
            font-size: 18px;
        }

        .month-grid {
            display: flex;
        }

        .it {
            flex: 1;
            text-align: center;
            margin: 1px 0;
        }

        .weekNumber {
            font-size: 9px;
        }

        .daysName {
            font-size: 10px;
            font-weight: bold;
        }

        .daysNumber {
            font-size: 12px;
            color: #050625;
        }

        .daysNumber span {
            padding: 2px 0;
            width: 19px;
            display: inline-block;
        }

        .pr {
            background: #404364;
            color: #f8f8fa;
            border-radius: 50%;
        }

        .prd {
            background: #6d8fd9;
            color: #050625;
            border-radius: 50%;
        }

        .vh {
            background: #353a9e;
            color: #f8f8fa;
            border-radius: 50%;
        }

        .tabel-head td {
            padding: 6px 1px;
            border-left: solid 1px #000;
        }

        .tabl td {
            vertical-align: top;
        }
        .tabl2 tr:first-child td:first-child {
            border-left: 1px solid #3a63bf;
            border-top: 1px solid #3a63bf;
            border-top-left-radius: 10px;
        }
        .tabl2 tr:first-child td:last-child {
            border-right: 1px solid #3a63bf;
            border-top: 1px solid #3a63bf;
            border-top-right-radius: 10px;
        }
        .tabl2 tr:last-child td:first-child {
            border-left: 1px solid #3a63bf;
            border-bottom: 1px solid #3a63bf;
            border-bottom-left-radius: 10px;
        }
        .tabl2 tr:last-child td:last-child {
            border-right: 1px solid #3a63bf;
            border-bottom: 1px solid #3a63bf;
            border-bottom-right-radius: 10px;
        }
        .tbl2row td{
            padding: 3px;
            border-top: 1px solid #3a63bf;
            border-right: 1px solid #3a63bf;
        }
    </style>
</head>
<body>
<h1 class="noprint" style="text-align: center; color: #fff;padding: 15px;">Конструктор календарей <sup
        style="font-size: 12px">ver. 0.1
        betta</sup></h1>
<div class="flex-container ">
    <div class="item1 noprint"></div>
    <div class="item2 content" id="printableArea">
        <div class="cwrap-container">
            <div class="cwrap-item1 tabl">
                <h1 style="text-align: center;margin-top: 20px;">Производственный календарь 2026</h1>
                <table style="margin: 5px 0 0 0; padding: 0; width: 100%;">
                    <tr>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Январь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">1</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="pr">1</span></div>
                                    <div class="it daysNumber"><span class="pr">2</span></div>
                                    <div class="it daysNumber"><span class="vh">3</span></div>
                                    <div class="it daysNumber"><span class="vh">4</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">2</div>
                                    <div class="it daysNumber"><span class="pr">5</span></div>
                                    <div class="it daysNumber"><span class="pr">6</span></div>
                                    <div class="it daysNumber"><span class="pr">7</span></div>
                                    <div class="it daysNumber"><span class="pr">8</span></div>
                                    <div class="it daysNumber"><span class="pr">9</span></div>
                                    <div class="it daysNumber"><span class="vh">10</span></div>
                                    <div class="it daysNumber"><span class="vh">11</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">3</div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span class="vh">17</span></div>
                                    <div class="it daysNumber"><span class="vh">18</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">4</div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span class="vh">24</span></div>
                                    <div class="it daysNumber"><span class="vh">25</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">5</div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span>31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Февраль
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">5</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="vh">1</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">6</div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span class="vh">7</span></div>
                                    <div class="it daysNumber"><span class="vh">8</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">7</div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span class="vh">14</span></div>
                                    <div class="it daysNumber"><span class="vh">15</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">8</div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span class="vh">21</span></div>
                                    <div class="it daysNumber"><span class="vh">22</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">9</div>
                                    <div class="it daysNumber"><span class="pr">23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span class="vh">28</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Март
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">9</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="vh">1</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">10</div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span class="vh">7</span></div>
                                    <div class="it daysNumber"><span class="vh">8</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">11</div>
                                    <div class="it daysNumber"><span class="pr">9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span class="vh">14</span></div>
                                    <div class="it daysNumber"><span class="vh">15</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">12</div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span class="vh">21</span></div>
                                    <div class="it daysNumber"><span class="vh">22</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">13</div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span class="vh">28</span></div>
                                    <div class="it daysNumber"><span class="vh">29</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">14</div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span>31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Апрель
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">14</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span class="vh">4</span></div>
                                    <div class="it daysNumber"><span class="vh">5</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">15</div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span class="vh">11</span></div>
                                    <div class="it daysNumber"><span class="vh">12</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">16</div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span class="vh">18</span></div>
                                    <div class="it daysNumber"><span class="vh">19</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">17</div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span class="vh">25</span></div>
                                    <div class="it daysNumber"><span class="vh">26</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">18</div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span class="prd">30</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Май
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">18</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="pr">1</span></div>
                                    <div class="it daysNumber"><span class="vh">2</span></div>
                                    <div class="it daysNumber"><span class="vh">3</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">19</div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span class="prd">7</span></div>
                                    <div class="it daysNumber"><span class="pr">8</span></div>
                                    <div class="it daysNumber"><span class="vh">9</span></div>
                                    <div class="it daysNumber"><span class="vh">10</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">20</div>
                                    <div class="it daysNumber"><span class="pr">11</span></div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span class="vh">16</span></div>
                                    <div class="it daysNumber"><span class="vh">17</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">21</div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span class="vh">23</span></div>
                                    <div class="it daysNumber"><span class="vh">24</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">22</div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span class="vh">30</span></div>
                                    <div class="it daysNumber"><span class="vh">31</span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Июнь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">23</div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span class="vh">6</span></div>
                                    <div class="it daysNumber"><span class="vh">7</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">24</div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span class="prd">11</span></div>
                                    <div class="it daysNumber"><span class="pr">12</span></div>
                                    <div class="it daysNumber"><span class="vh">13</span></div>
                                    <div class="it daysNumber"><span class="vh">14</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">25</div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span class="vh">20</span></div>
                                    <div class="it daysNumber"><span class="vh">21</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">26</div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span class="vh">27</span></div>
                                    <div class="it daysNumber"><span class="vh">28</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">27</div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Июль
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">27</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span class="vh">4</span></div>
                                    <div class="it daysNumber"><span class="vh">5</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">28</div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span class="vh">11</span></div>
                                    <div class="it daysNumber"><span class="vh">12</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">29</div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span class="vh">18</span></div>
                                    <div class="it daysNumber"><span class="vh">19</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">30</div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span class="vh">25</span></div>
                                    <div class="it daysNumber"><span class="vh">26</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">31</div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span>31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Август
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">31</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="vh">1</span></div>
                                    <div class="it daysNumber"><span class="vh">2</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">32</div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span class="vh">8</span></div>
                                    <div class="it daysNumber"><span class="vh">9</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">33</div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span class="vh">15</span></div>
                                    <div class="it daysNumber"><span class="vh">16</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">34</div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span class="vh">22</span></div>
                                    <div class="it daysNumber"><span class="vh">23</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">35</div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span class="vh">29</span></div>
                                    <div class="it daysNumber"><span class="vh">30</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">36</div>
                                    <div class="it daysNumber"><span>31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Сентябрь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">36</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span class="vh">5</span></div>
                                    <div class="it daysNumber"><span class="vh">6</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">37</div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span class="vh">12</span></div>
                                    <div class="it daysNumber"><span class="vh">13</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">38</div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span class="vh">19</span></div>
                                    <div class="it daysNumber"><span class="vh">20</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">39</div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span class="vh">26</span></div>
                                    <div class="it daysNumber"><span class="vh">27</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">40</div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Октябрь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">40</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span class="vh">3</span></div>
                                    <div class="it daysNumber"><span class="vh">4</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">41</div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span class="vh">10</span></div>
                                    <div class="it daysNumber"><span class="vh">11</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">42</div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span class="vh">17</span></div>
                                    <div class="it daysNumber"><span class="vh">18</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">43</div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span class="vh">24</span></div>
                                    <div class="it daysNumber"><span class="vh">25</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">44</div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span class="vh">31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Ноябрь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">44</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span class="vh">1</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">45</div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span class="prd">3</span></div>
                                    <div class="it daysNumber"><span class="pr">4</span></div>
                                    <div class="it daysNumber"><span>5</span></div>
                                    <div class="it daysNumber"><span>6</span></div>
                                    <div class="it daysNumber"><span class="vh">7</span></div>
                                    <div class="it daysNumber"><span class="vh">8</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">46</div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span>12</span></div>
                                    <div class="it daysNumber"><span>13</span></div>
                                    <div class="it daysNumber"><span class="vh">14</span></div>
                                    <div class="it daysNumber"><span class="vh">15</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">47</div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span>19</span></div>
                                    <div class="it daysNumber"><span>20</span></div>
                                    <div class="it daysNumber"><span class="vh">21</span></div>
                                    <div class="it daysNumber"><span class="vh">22</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">48</div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span>26</span></div>
                                    <div class="it daysNumber"><span>27</span></div>
                                    <div class="it daysNumber"><span class="vh">28</span></div>
                                    <div class="it daysNumber"><span class="vh">29</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">49</div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="month">
                                <div class="month-name">
                                    Декабрь
                                </div>
                                <div class="month-grid" style="margin: 0 0 3px 0">
                                    <div class="it daysName">&nbsp;</div>
                                    <div class="it daysName">Пн</div>
                                    <div class="it daysName">Вт</div>
                                    <div class="it daysName">Ср</div>
                                    <div class="it daysName">Чт</div>
                                    <div class="it daysName">Пт</div>
                                    <div class="it daysName">Сб</div>
                                    <div class="it daysName">Вс</div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">49</div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span>1</span></div>
                                    <div class="it daysNumber"><span>2</span></div>
                                    <div class="it daysNumber"><span>3</span></div>
                                    <div class="it daysNumber"><span>4</span></div>
                                    <div class="it daysNumber"><span class="vh">5</span></div>
                                    <div class="it daysNumber"><span class="vh">6</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">50</div>
                                    <div class="it daysNumber"><span>7</span></div>
                                    <div class="it daysNumber"><span>8</span></div>
                                    <div class="it daysNumber"><span>9</span></div>
                                    <div class="it daysNumber"><span>10</span></div>
                                    <div class="it daysNumber"><span>11</span></div>
                                    <div class="it daysNumber"><span class="vh">12</span></div>
                                    <div class="it daysNumber"><span class="vh">13</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">51</div>
                                    <div class="it daysNumber"><span>14</span></div>
                                    <div class="it daysNumber"><span>15</span></div>
                                    <div class="it daysNumber"><span>16</span></div>
                                    <div class="it daysNumber"><span>17</span></div>
                                    <div class="it daysNumber"><span>18</span></div>
                                    <div class="it daysNumber"><span class="vh">19</span></div>
                                    <div class="it daysNumber"><span class="vh">20</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">52</div>
                                    <div class="it daysNumber"><span>21</span></div>
                                    <div class="it daysNumber"><span>22</span></div>
                                    <div class="it daysNumber"><span>23</span></div>
                                    <div class="it daysNumber"><span>24</span></div>
                                    <div class="it daysNumber"><span>25</span></div>
                                    <div class="it daysNumber"><span class="vh">26</span></div>
                                    <div class="it daysNumber"><span class="vh">27</span></div>
                                </div>
                                <div class="month-grid">
                                    <div class="it weekNumber">53</div>
                                    <div class="it daysNumber"><span>28</span></div>
                                    <div class="it daysNumber"><span>29</span></div>
                                    <div class="it daysNumber"><span>30</span></div>
                                    <div class="it daysNumber"><span class="pr">31</span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                    <div class="it daysNumber"><span></span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="cwrap-item2" style="text-align: center">
                <h2 style="padding-top: 10px;">Нормы рабочего времени на 2026 год</h2>
                <table class="tabl2" style="margin-top: 15px; padding:0;  width: 100%; font-size: 12px; border-spacing: 0; border-collapse: separate;">
                    <tr >
                        <td rowspan="2" style="padding-top: 5px;background: #adc8e5;border-right: 1px solid #3a63bf;">Период</td>
                        <td colspan="3" style="padding-top: 5px;background: #adc8e5;border-top: 1px solid #3a63bf;border-right: 1px solid #3a63bf;">Количество дней</td>
                        <td colspan="3" style="padding-top: 5px;background: #adc8e5;">Рабочих часов при неделе</td>
                    </tr>
                    <tr>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;">Календарных</td>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;">Рабочих</td>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;border-right: 1px solid #3a63bf;">Выходных</td>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;">40 часов</td>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;">36 часов</td>
                        <td style="font-size: 11px;padding: 3px;background: #adc8e5;border-right: 1px solid #3a63bf;">24 часа</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Январь</td>
                        <td>31</td>
                        <td>15</td>
                        <td>16</td>
                        <td>120</td>
                        <td>108</td>
                        <td>72</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Февраль</td>
                        <td>28</td>
                        <td>19</td>
                        <td>9</td>
                        <td>152</td>
                        <td>136.8</td>
                        <td>91.2</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Март</td>
                        <td>31</td>
                        <td>21</td>
                        <td>10</td>
                        <td>168</td>
                        <td>151.2</td>
                        <td>100.8</td>
                    </tr>
                    <tr class="tbl2row" style="background: #89a6d2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">1 квартал</td>
                        <td>90</td>
                        <td>55</td>
                        <td>35</td>
                        <td>440</td>
                        <td>396</td>
                        <td>264</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Апрель</td>
                        <td>30</td>
                        <td>22</td>
                        <td>8</td>
                        <td>175</td>
                        <td>157.4</td>
                        <td>104.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Май</td>
                        <td>31</td>
                        <td>19</td>
                        <td>12</td>
                        <td>151</td>
                        <td>135.8</td>
                        <td>90.2</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Июнь</td>
                        <td>30</td>
                        <td>21</td>
                        <td>9</td>
                        <td>167</td>
                        <td>150.2</td>
                        <td>99.8</td>
                    </tr>
                    <tr class="tbl2row" style="background: #89a6d2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">2 квартал</td>
                        <td>91</td>
                        <td>62</td>
                        <td>29</td>
                        <td>493</td>
                        <td>443.4</td>
                        <td>294.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: #595ea2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">1 полугодие</td>
                        <td>181</td>
                        <td>117</td>
                        <td>64</td>
                        <td>933</td>
                        <td>839.4</td>
                        <td>558.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Июль</td>
                        <td>31</td>
                        <td>23</td>
                        <td>8</td>
                        <td>184</td>
                        <td>165.6</td>
                        <td>110.4</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Август</td>
                        <td>31</td>
                        <td>21</td>
                        <td>10</td>
                        <td>168</td>
                        <td>151.2</td>
                        <td>100.8</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Сентябрь</td>
                        <td>30</td>
                        <td>22</td>
                        <td>8</td>
                        <td>176</td>
                        <td>158.4</td>
                        <td>105.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: #89a6d2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">3 квартал</td>
                        <td>92</td>
                        <td>66</td>
                        <td>26</td>
                        <td>528</td>
                        <td>475.2</td>
                        <td>316.8</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Октябрь</td>
                        <td>31</td>
                        <td>22</td>
                        <td>9</td>
                        <td>176</td>
                        <td>158.4</td>
                        <td>105.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Ноябрь</td>
                        <td>30</td>
                        <td>20</td>
                        <td>10</td>
                        <td>159</td>
                        <td>143</td>
                        <td>95</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">Декабрь</td>
                        <td>31</td>
                        <td>22</td>
                        <td>9</td>
                        <td>176</td>
                        <td>158.4</td>
                        <td>105.6</td>
                    </tr>
                    <tr class="tbl2row" style="background: #89a6d2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">4 квартал</td>
                        <td>92</td>
                        <td>64</td>
                        <td>28</td>
                        <td>511</td>
                        <td>459.8</td>
                        <td>306.2</td>
                    </tr>
                    <tr class="tbl2row" style="background: #595ea2; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">2 полугодие</td>
                        <td>184</td>
                        <td>130</td>
                        <td>54</td>
                        <td>1039</td>
                        <td>935</td>
                        <td>623</td>
                    </tr>
                    <tr class="tbl2row" style="background: #1e205d; color: #fff">
                        <td style="border-left: 1px solid #3a63bf;">2026 год</td>
                        <td style="border-bottom: 1px solid #3a63bf;">365</td>
                        <td style="border-bottom: 1px solid #3a63bf;">247</td>
                        <td style="border-bottom: 1px solid #3a63bf;">118</td>
                        <td style="border-bottom: 1px solid #3a63bf;">1972</td>
                        <td style="border-bottom: 1px solid #3a63bf;">1774.4</td>
                        <td>1181.6</td>
                    </tr>
                </table>
                <h2 style="padding-top: 10px;">Праздничные и сокращенные дни</h2>
                <table class="tabl2" style="margin-top: 15px; padding:0;  width: 100%; font-size: 12px; border-spacing: 0; border-collapse: separate;">
                    <tr>
                        <td style="padding: 5px;background: #adc8e5;border-right: 1px solid #3a63bf;">Праздничные дни</td>
                        <td style="padding: 5px;background: #adc8e5;border-right: 1px solid #3a63bf;border-top: 1px solid #3a63bf;">Праздник</td>
                        <td style="padding: 5px;background: #adc8e5;border-right: 1px solid #3a63bf;border-top: 1px solid #3a63bf;">Переносы выходных дней</td>
                        <td style="padding: 5px;background: #adc8e5;border-right: 1px solid #3a63bf;">Сокращенные дни</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">1-6, 8 января</td>
                        <td>Новогодние каникулы</td>
                        <td>с 3 января на 9 января</td>
                        <td></td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">7 января</td>
                        <td>Рождество Христово</td>
                        <td>с 4 января на 31 декабря</td>
                        <td></td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">23 февраля</td>
                        <td>День защитника Отечества</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">8 марта</td>
                        <td>Международный женский день</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">1 мая</td>
                        <td>Праздник весны и труда</td>
                        <td></td>
                        <td>30 апреля</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">9 мая</td>
                        <td>День Победы</td>
                        <td></td>
                        <td>7 мая</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">12 июня</td>
                        <td>День России</td>
                        <td></td>
                        <td>11 июня</td>
                    </tr>
                    <tr class="tbl2row" style="background: transparent;">
                        <td style="border-left: 1px solid #3a63bf;">4 ноября</td>
                        <td style="border-bottom: 1px solid #3a63bf;">День народного единства</td>
                        <td style="border-bottom: 1px solid #3a63bf;"></td>
                        <td>3 ноября</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="item3 noprint"></div>
</div>
</body>
</html>

