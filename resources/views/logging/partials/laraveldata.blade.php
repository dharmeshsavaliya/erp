@foreach ($logs as $log)

                <tr>
                     <td>{{ $log->filename }}</td>
                    <td class="expand-row table-hover-cell"><span class="td-mini-container">
                        {{ strlen( $log->log ) > 60 ? substr( $log->log , 0, 60).'...' :  $log->log }}
                        </span>
                        <span class="td-full-container hidden">
                        {{ $log->log }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($log->log_created)->format('d-m-y H:i:s')  }}</td>
                </tr>
@endforeach