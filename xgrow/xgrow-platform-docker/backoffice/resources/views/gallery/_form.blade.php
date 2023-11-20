		 @if (count($errors) > 0)
            <div class="row">
                <div class="col col-sm-6 offset-sm-3">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                {{ $error }} <br />
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

		{!! Form::model($gallery,['method'=>'put', 'enctype' => 'multipart/form-data', 'route'=>array('gallery.store', "id=".$gallery->id)]) !!}

    	<div class="row">

	    	<div class="col-md-6">
	    		<div class="form-group">
		          {!! Form::label('name','*Nome:') !!}
		          {!! Form::text('name',
		                          null,
		                          [
		                          'class'     =>'form-control',
		                          'required'
		                           ]) !!}
		    	</div>
		    </div>

    	</div>

    	<div class="row">

	    	 <div class="col-md-6">
	    		<div class="form-group">
		          {!! Form::label('description','Descrição:') !!}
		          {!! Form::textarea('description',
		                          null,
		                          [
		                          'class'    => 'form-control',
		                          'rows'     => '2',
		                           ]) !!}
		    	</div>
		    </div>

    	</div>

	<div class="row">
	      <div class="col-md-4">
	      		<div class='form-group'>
	            {!! Form::submit('Salvar',['class'=>'btn btn-primary btn-primary-custom']) !!}
	            </div>
	      </div>
     </div>


      {!! Form::close() !!}

  </form>