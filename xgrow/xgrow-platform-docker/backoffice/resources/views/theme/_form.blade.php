
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

		{!! Form::model($template,['method'=>'put', 'enctype' => 'multipart/form-data', 'route'=>array('template.store', "id=".$template->id)]) !!}

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

		    	<div class="col-md-6">
		    		<div class="form-group">
			          {!! Form::label('folder','*Pasta:') !!}
			          {!! Form::text('folder',
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
			                          'class'     =>'form-control',
			                          'rows'     =>'4',
			                           ]) !!}
			    	</div>
			    </div>
    	

	    		<div class="col-md-3">
		    		<div class="form-group">
			          {!! Form::label('amount_of_fixed_content','*Qtd. de conteúdo fixo:') !!}
			          {!! Form::number('amount_of_fixed_content',
			                          null,
			                          [
			                          'class'     =>'form-control',
				                          'required'
			                           ]) !!}
			    	</div>
					<div class="form-group">
			          {!! Form::label('tamanho_imagem','Sugestão tamanho imagem:') !!}
			          {!! Form::text('tamanho_imagem',
			                          null,
			                          [
			                          'class'     =>'form-control',
				                    
			                           ]) !!}
			    	</div>
					
			    </div>
    		
	    		<div class="col-md-3">
		    		<div class="form-group">
			          {!! Form::checkbox('has_slide',
			                          		null
			                      		) !!}
			          {!! Form::label('has_slide','Possui slide') !!}  
			    	</div>
			    </div>
    		
    	</div>


    	<div class="row">

			<div class="col-md-6">
	    		<div class="form-group">
		            {!! Form::label('icon','Icone:') !!}
					{!! Form::file('icon', ['class' => 'form-control']) !!}
		        </div>
	    	</div>

	    	@if($template->id > 0)
		    	<div class="col-md-6">
		    		<div class="form-group">
			            <img src="{{ asset('uploads'. '/' . $template->thumb->filename) }}" style="width: 128px; height: auto" >
			        </div>
		    	</div>
	    	@endif

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