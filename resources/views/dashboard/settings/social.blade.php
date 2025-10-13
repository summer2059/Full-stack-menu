<div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
    <div class="col-xl-12 mt-2">
        <div class="card height-equal">
            <div class="card-body custom-input">
                <div class="row g-3">
                    <div class="form-group{{ $errors->has('facebook_link') ? ' has-error' : '' }}">
                        <label for="facebook_link" class="col-sm-2 control-label">Facebook Link</label>
                        <div class="col-sm-10">
                            <input type="text" name="facebook_link" class="form-control" id="facebook_link"
                                   value="{{ getConfiguration('facebook_link') }}">
                            @if ($errors->has('facebook_link'))
                                <span class="help-block">
                                    {{ $errors->first('facebook_link') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('linkedin_link') ? ' has-error' : '' }}">
                        <label for="linkedin_link" class="col-sm-2 control-label">Linkedin Link</label>
                        <div class="col-sm-10">
                            <input type="text" name="linkedin_link" class="form-control" id="linkedin_link"
                                   value="{{ getConfiguration('linkedin_link') }}">
                            @if ($errors->has('linkedin_link'))
                                <span class="help-block">
                                    {{ $errors->first('linkedin_link') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('instagram_link') ? ' has-error' : '' }}">
                        <label for="instagram_link" class="col-sm-2 control-label">Instagram Link</label>
                        <div class="col-sm-10">
                            <input type="text" name="instagram_link" class="form-control" id="instagram_link"
                                   value="{{ getConfiguration('instagram_link') }}">
                            @if ($errors->has('instagram_link'))
                                <span class="help-block">
                                    {{ $errors->first('instagram_link') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('youtube_link') ? ' has-error' : '' }}">
                        <label for="youtube_link" class="col-sm-2 control-label">Youtube Link</label>
                        <div class="col-sm-10">
                            <input type="text" name="youtube_link" class="form-control" id="youtube_link"
                                   value="{{ getConfiguration('youtube_link') }}">
                            @if ($errors->has('youtube_link'))
                                <span class="help-block">
                                    {{ $errors->first('youtube_link') }}
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
