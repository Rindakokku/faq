@extends('layouts.app')

@section('content')
    <div class="container">
        <input type="text" name="tags" placeholder="Search hashtags" class="tm-input tm-input-success"/>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Questions
                        <a class="btn btn-primary float-right" href="{{ route('questions.create') }}">
                            Create a Question
                        </a>

                        <div class="card-body">

                            <div id="questions-deck" class="card-deck">
                                @forelse($questions as $question)
                                    <div class="col-sm-4 d-flex align-items-stretch">
                                        <div class="card mb-3 ">
                                            <div class="card-header">
                                                <small class="text-muted">
                                                    Updated: {{ $question->created_at->diffForHumans() }}
                                                    Answers: {{ $question->answers()->count() }}

                                                </small>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">{{$question->body}}</p>
                                            </div>
                                            <div class="card-footer">
                                                <p class="card-text">

                                                    <a class="btn btn-primary float-right"
                                                       href="{{ route('questions.show', ['id' => $question->id]) }}">
                                                        View
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    There are no questions to view, you can  create a question.
                                @endforelse
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="float-right">
                                {{ $questions->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(".tm-input").tagsManager();
        $(".tm-input").on('tm:refresh', function (e, taglist) {
            if (taglist === "") {
                // Reload page to cause all questions to show
                location.reload();
            }
            $.getJSON("{{route('questions.search', [])}}?tags=" + taglist, function (data) {
                $("#questions-deck").html(``);
                $.each(data, function (i, question) {
                    console.log(question);
                    var questionsHtml = `
                        <div class="col-sm-4 d-flex align-items-stretch"><div class="card mb-3 "><div class="card-header"><small class="text-muted">
                            Updated: ${question.created_at}</small>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${question.body}</p>
                        </div>
                        <div class="card-footer"><p class="card-text"><a class="btn btn-primary float-right">View</a></p></div></div></div>`;
                    $("#questions-deck").append(questionsHtml);
                });
            });
        });
    </script>
@endsection
