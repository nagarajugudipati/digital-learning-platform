@extends('layouts.teacher')
@section('title', 'Chatbot Q&A Training')
@section('teacher-content')
@include('chatbot-qa._page', ['routePrefix' => 'teacher'])
@endsection
