@extends('layouts.main')


@section('content')
<div class="modal fade" id="PositionModal" tabindex="-1" role="dialog" aria-labelledby="PositionModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="PositionModalTitle">Position</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn1" data-dismiss="modal"></button>
            <button type="button" class="btn btn-primary btn2"></button>
            </div>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <div class="px-4 py-3">
        @can( 'create', [ App\Models\Position::class ] )<a href="{{ route('position.create') }}" class="btn btn-sm btn-secondary float-right">Add position</a>@endcan
        <h2 class="m-0">Positions</h2>

        <div class="mt-3">
            <table class="table table-bordered yajra-datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Last update</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection


@section('script')

$(function () {

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [],
            aaSorting: [],
            ajax: "{{ route('positions.index') }}",
            columns: [
                {data: 'title', name: 'title', orderable: true, searchable: true},
                {
                    data: 'updated_at', name: 'updated_at', orderable: true, searchable: false,
                    render: function (data, type, row, meta) {
                        return moment(data).format('DD.MM.YYYY');

                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            fnDrawCallback: function(){
                $.each($('.deletePosition'),function(i,data){
                    $(data).on('click',function(){
                        PositionDelete($(this).data('position'));
                    });
                });
            }
        });
});

function PositionDelete(id){
    var url = '{{ route('position.delete', ':id') }}';
    $('#deletePositionFA-'+id)
        .removeClass('far fa-trash-alt')
        .addClass('fas fa-spinner fa-spin');
    axios.get(url.replace(':id', id), {})
        .then(function (response) {
            $('#PositionModal .modal-body').html(response.data['message']);
            if (typeof response.data['btn1'] !== 'undefined' && response.data['btn1']>0){
                $('#PositionModal .btn1').show();
            } else {
                $('#PositionModal .btn1').hide();
            }
            if (typeof response.data['btn2'] !== 'undefined' && response.data['btn2']>0){
                $('#PositionModal .btn2').show();
            } else {
                $('#PositionModal .btn2').hide();
            }

            $('#PositionModal .btn1').html(response.data['btn1_title']);
            $('#PositionModal .btn2').html(response.data['btn2_title']);
            if (typeof response.data['success'] !== 'undefined' && response.data['success'] > 0){
                $('#PositionModal .btn2').on('click',function(){
                    $(this).html('<i class="fas fa-spinner fa-spin"></i>');
                    var url = '{{ route('position.destroy', ':id') }}';
                    axios.get(url.replace(':id', id), {
                            'headers': {
                                'Accept':'application/json'
                            }
                        })
                        .then(function (response1) {
                            if (typeof response1.data['success'] !== 'undefined' && response1.data['success'] > 0){
                                location.href = '{{route('positions.index')}}';
                            } else {
                                alert('Error (no success). Check console.');
                                $('#PositionModal .btn2').html(response.data['btn2_title']);
                                console.log(response1);
                            }
                        })
                        .catch(function (error) {
                            if ( typeof error.response['message'] !== 'undefined' ){
                                $('#PositionModal .btn1').show();
                                $('#PositionModal .btn2').hide();
                                $('#PositionModal .modal-body').html(error.response['message']);
                                $('#PositionModal .btn2').html(response.data['btn2_title']);
                                $('#PositionModal').modal({});
                            } else {
                                $('#PositionModal .btn2').html(response.data['btn2_title']);
                                alert('Error (catch). Check console.');
                                console.log(error.response);
                            }
                        });
                });
            } else {
                if ( typeof response.data['message'] !== 'undefined' ){
                    $('#PositionModal .btn1').show();
                    $('#PositionModal .btn2').hide();
                    $('#PositionModal .modal-body').html(response.data['message']);
                    $('#PositionModal .btn2').html(response.data['btn2_title']);
                    $('#PositionModal').modal({});
                } else {
                    alert('Error. Check console.');
                    console.log(response);
                }
            }
            $('#PositionModal').on('hidden.bs.modal', function () {
                $('#deletePositionFA-'+id)
                    .removeClass('fas fa-spinner fa-spin')
                    .addClass('far fa-trash-alt');
            })
        })
        .catch(function (error) {
            alert('Error. Check console.');
            console.log(error);
        });

}

function PositionDestroy()
{
    var url = '{{ route('position.destroy', ':id') }}';
    axios.get(url.replace(':id', id), {
        firstName: 'Fred',
        lastName: 'Flintstone'
    })
    .then(function (response) {

    })
    .catch(function (error) {
        console.log(error);
    });
}

@endsection
