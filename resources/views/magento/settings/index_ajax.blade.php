

                    
                        @foreach ($magentoSettings as $magentoSetting)
                            <tr>
                                <td>{{ $magentoSetting->id }}</td>

                                                                @if($magentoSetting->scope === 'default')

                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->website->website; ?>')" >{{  substr($magentoSetting->website->website, 0,10) }} @if(strlen($magentoSetting->website->website) > 10) ... @endif</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('sololuxurydubai@gmail.com')" >-</td>
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('sololuxurydubai@gmail.com')" >-</td>

                                @elseif($magentoSetting->scope === 'websites')
                                
                                        <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->website : '-' ; ?>')" >
                                            {{ $magentoSetting->store &&  $magentoSetting->store->website &&  $magentoSetting->store->website->storeWebsite ? $magentoSetting->store->website->storeWebsite->website : '-' }} ...
                                        </td>
                                        <td ata-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->website->website; ?>')" >{{ substr($magentoSetting->store->website->name, 0,10) }} @if(strlen($magentoSetting->store->website->name) > 10) ... @endif</td>
                                        <td>-</td>
                                        
                                @else
                                        <td>{{ $magentoSetting->storeview && $magentoSetting->storeview->websiteStore && $magentoSetting->storeview->websiteStore->website && $magentoSetting->storeview->websiteStore->website->storeWebsite ? $magentoSetting->storeview->websiteStore->website->storeWebsite->website : '-' }}</td>
                                        <td ata-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->website->website; ?>')" >{{   substr($magentoSetting->storeview && $magentoSetting->storeview->websiteStore ? $magentoSetting->storeview->websiteStore->name : '-', 0,10) }}</td>
                                        <td>{{ $magentoSetting->storeview->code }}</td>
                                @endif

                                <td>{{ $magentoSetting->scope }}</td>
                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->name ; ?>')" >{{ substr($magentoSetting->name,0,12) }} @if(strlen($magentoSetting->name) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->path ; ?>')" >{{ substr($magentoSetting->path,0,12) }} @if(strlen($magentoSetting->path) > 12) ... @endif</td>

                                <td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php echo $magentoSetting->value ; ?>')" >{{ substr($magentoSetting->value,0,12) }} @if(strlen($magentoSetting->value) > 12) ... @endif</td>
								<td data-toggle="modal" data-target="#viewMore" onclick="opnModal('<?php if(isset($newValues[$magentoSetting['id']])) {echo  $newValues[$magentoSetting['id']];   } ?>')">


                                    @if(isset($newValues[$magentoSetting['id']])) {{ substr( $newValues[$magentoSetting['id']], 0,10) }} @if(strlen($newValues[$magentoSetting['id']]) > 10) ... @endif @endif


                                </td>
                                <td>{{ $magentoSetting->created_at }}</td>
                                <td>{{ $magentoSetting->status }}</td>
                                <td>{{ $magentoSetting->uname }}</td>
                                <td>
                                    <button type="button" value="{{ $magentoSetting->scope }}" class="btn btn-image edit-setting p-0" data-setting="{{ json_encode($magentoSetting) }}" ><img src="/images/edit.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image delete-setting p-0" ><img src="/images/delete.png"></button>
                                    <button type="button" data-id="{{ $magentoSetting->id }}" class="btn btn-image push_logs p-0" ><i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                        @endforeach
                  