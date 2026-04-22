@extends('layouts.admin')
@section('title', 'Chatbot Q&A Training')
@section('admin-content')
@include('chatbot-qa._page', ['routePrefix' => 'admin'])
@endsection
