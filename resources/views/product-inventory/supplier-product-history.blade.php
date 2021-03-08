@extends('layouts.app')

@section('title', 'Supplier Inventory History')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Supplier Inventory History ({{$total_rows}})</h2>
        </div>

        <div class="col-12">
          <div class="pull-left"></div>

          <div class="pull-right">
            <div class="form-group">
              &nbsp;
            </div>
          </div>
        </div>
    </div>

    @include('partials.flash_messages')



<form method="get" action="{{route('supplier.product.history')}}">

     <div class="form-group">
                        <div class="row">
                            
                            
                            

                            <div class="col-md-3">
                               <select class="form-control select-multiple" id="supplier-select" tabindex="-1" aria-hidden="true" name="supplier" onchange="//showStores(this)">
                                    <option value="">Select Supplier</option>

                                    @foreach($suppliers as $supplier)

                                    @if(isset($request->supplier) && $supplier->id==$request->supplier)

                                     <option value="{{$supplier->id}}" selected="selected">{{$supplier->supplier}}</option>


                                    @else

                                     <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>


                                    @endif

                                   
                                         @endforeach
                                        </select>
                            </div>

                            
                            <div class="col-md-1 d-flex justify-content-between">
                               <button type="submit" class="btn btn-image" ><img src="/images/filter.png"></button><button type="button" onclick="resetForm(this)" class="btn btn-image" id=""><img src="/images/resend2.png"></button>  
                            </div>
                          <!--   <div class="col-md-1">
                                  
                            </div> -->
                        </div>

                    </div>

</form>


    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                
                      
         

                    <tr>
                       
                        <th>Supplier Name</th> 
                      
                        <th>Products</th>

                        <th> Brands </th>

                        <th>Summary</th>
                        
                      
                      
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allHistory as $key=> $row ) 
                    <tr>
                      
                      <td>{{$row['supplier_name']}}</td>
                     <td>{{$row['products']}}</td>
                     <td>{{$row['brands']}}</td>
                     

                    
                     <td class="showSummary"><a target="_blank" href="{{route('supplier.product.summary',$row['supplier_id'])}}">Details</td>
                    </tr>

                    @endforeach
                    <tr>
                         <td colspan="10">
        {{ $inventory->appends(request()->except("page"))->links() }}
    </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
@endsection

@section('scripts')

<script type="text/javascript">
 

    function resetForm(selector)
        {
            
           $(selector).closest('form').find('input,select').val('');

           $(selector).closest('form').submit();
        }



</script>

@endsection



