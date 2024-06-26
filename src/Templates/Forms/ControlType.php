<?php

namespace PowerDI\Templates\Forms;

enum ControlType : string {
    case Text = "text";
    case Date = "date";
    case File = "file";
    case Number = "number";
    case Select = "select";
}