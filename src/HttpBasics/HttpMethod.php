<?php
namespace PowerDI\HttpBasics;

enum HttpMethod: string
{
    case GET = "GET";
    case POST = "POST";
    case DELETE = "DELETE";
    case UPDATE = "UPDATE";
    case HEAD = "HEAD";
    case PUT = "PUT";
    case CONNECT = "CONNECT";
    case OPTIONS = "OPTIONS";
    case TRACE = "TRACE";
    case PATCH = "PATCH";
}

