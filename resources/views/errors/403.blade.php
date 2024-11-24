@extends('errors::minimal')

@section('title', 'خطای دسترسی')
@section('code', '403')
@section('message', $exception->getMessage() ?: 'خطای دسترسی')
