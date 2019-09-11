@extends('bookstack::simple-layout')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('bookstack::partials.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/delete') => [
                    'text' => trans('entities.pages_delete'),
                    'icon' => 'delete',
                ]
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ $page->draft ? trans('bookstack::entities.pages_delete_draft') : trans('bookstack::entities.pages_delete') }}</h1>


            <div class="grid half v-center">
                <div>
                    <p class="text-neg">
                        <strong>
                            {{ $page->draft ? trans('bookstack::entities.pages_delete_draft_confirm'): trans('bookstack::entities.pages_delete_confirm') }}
                        </strong>
                    </p>
                </div>
                <div>
                    <form action="{{ $page->getUrl() }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="DELETE">
                        <div class="form-group text-right">
                            <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('bookstack::common.cancel') }}</a>
                            <button type="submit" class="button">{{ trans('bookstack::common.confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop