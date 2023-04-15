@forelse ($result as $file)
    <tr>
        <td>{{$file['file_name']}}</td>
        <td>{{$file['file_creation_date']}}</td>
        <td>
            <input class="fileUrl" type="text" value="{{env('GOOGLE_DRIVE_FILE_URL').$file['google_drive_file_id']}}/view?usp=share_link" />
            <button class="copy-button btn btn-secondary" data-message="{{env('GOOGLE_DRIVE_FILE_URL').$file['google_drive_file_id']}}/view?usp=share_link">Copy</button>
        </td>
        <td>{{$file['remarks']}}</td>
    </tr>

@empty
    <tr><td colspan="4">No record found</td></tr>
@endforelse
