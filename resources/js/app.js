import './bootstrap';
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import DateRangePicker from './daterangepicker';
window.DateRangePicker = DateRangePicker;

import moment from 'moment';
window.moment = moment;

import { SetAlertText, DisplayAlert} from '/resources/js/ComponentJS/alertJS.js';
window.displayAlert = DisplayAlert;
window.setAlertText = SetAlertText;