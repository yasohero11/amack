@foreach($authors as $author)
    <tr>
        <td>
            <div class="d-flex px-2">
                <div class="a-avatar mx-2">
                    <img src="{{$author["image"] ?? asset('img/small-logos/logo-spotify.svg')}}" class="img-fluid"
                         alt="spotify">
                </div>
                <div class="my-auto">
                    <h6 class="mb-0 text-sm">{{$author["name"]}}</h6>
                </div>
            </div>
        </td>
        <td>
            <p class="text-sm font-weight-normal mb-0">{{$author["age"]}}</p>
        </td>
        <td>
            <span class="text-sm font-weight-normal">{{$author["address"]}}</span>
        </td>
        <td class="align-middle">
            <div class="d-flex">
                <span class="text-sm font-weight-normal">{{$author["role"]}}</span>
            </div>
        </td>
        <td class="align-middle">
            <div class="d-flex">
                <span class="text-sm font-weight-normal">{{$author["country"]["name"] ?? ""}}</span>
            </div>
        </td>

        <td class="align-middle">
            <a href="javascript:showModel('{{$author["id"]}}' ,  JSON.stringify({{json_encode($author)}}) );"
               class="text-secondary font-weight-bold text-xs" data-bs-toggle="tooltip" data-bs-title="Edit user">
                <svg width="14" height="14" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.2201 2.02495C10.8292 1.63482 10.196 1.63545 9.80585 2.02636C9.41572 2.41727 9.41635 3.05044 9.80726 3.44057L11.2201 2.02495ZM12.5572 6.18502C12.9481 6.57516 13.5813 6.57453 13.9714 6.18362C14.3615 5.79271 14.3609 5.15954 13.97 4.7694L12.5572 6.18502ZM11.6803 1.56839L12.3867 2.2762L12.3867 2.27619L11.6803 1.56839ZM14.4302 4.31284L15.1367 5.02065L15.1367 5.02064L14.4302 4.31284ZM3.72198 15V16C3.98686 16 4.24091 15.8949 4.42839 15.7078L3.72198 15ZM0.999756 15H-0.000244141C-0.000244141 15.5523 0.447471 16 0.999756 16L0.999756 15ZM0.999756 12.2279L0.293346 11.5201C0.105383 11.7077 -0.000244141 11.9624 -0.000244141 12.2279H0.999756ZM9.80726 3.44057L12.5572 6.18502L13.97 4.7694L11.2201 2.02495L9.80726 3.44057ZM12.3867 2.27619C12.7557 1.90794 13.3549 1.90794 13.7238 2.27619L15.1367 0.860593C13.9869 -0.286864 12.1236 -0.286864 10.9739 0.860593L12.3867 2.27619ZM13.7238 2.27619C14.0917 2.64337 14.0917 3.23787 13.7238 3.60504L15.1367 5.02064C16.2875 3.8721 16.2875 2.00913 15.1367 0.860593L13.7238 2.27619ZM13.7238 3.60504L3.01557 14.2922L4.42839 15.7078L15.1367 5.02065L13.7238 3.60504ZM3.72198 14H0.999756V16H3.72198V14ZM1.99976 15V12.2279H-0.000244141V15H1.99976ZM1.70617 12.9357L12.3867 2.2762L10.9739 0.86059L0.293346 11.5201L1.70617 12.9357Z"
                          fill="#64748B"/>
                </svg>
            </a>
            <a href="javascript:confirmModel.show({{$author["id"]}});" style="margin-left: 10px"
               class="text-secondary font-weight-bold text-xs " data-bs-toggle="tooltip" data-bs-title="Remove Country">
                <i style="font-size: 14px" class="fa-regular fa-trash-can"></i>
            </a>
        </td>
    </tr>
@endforeach



