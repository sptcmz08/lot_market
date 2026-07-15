@extends('layouts.admin')

@section('title', 'รายละเอียดงานติดตั้งเต็นท์')
@section('page_title', 'รายละเอียดงานติดตั้ง')

@section('styles')
<style>
    @media (max-width: 991px) {
        .task-detail-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.tasks.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> กลับไปหน้ารายการงานติดตั้ง
        </a>
    </div>

    <div class="task-detail-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Left Side: Task details, Installer photos, Problem Note -->
        <div>
            <!-- Detail Card -->
            <div class="cute-card">
                <h3 class="cute-card-title">
                    <i class="fa-solid fa-truck-ramp-box"></i> ข้อมูลงานจัดส่งและติดตั้ง
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px 30px;">
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">รหัสใบจองที่เกี่ยวข้อง:</span>
                        @if ($task->booking)
                            <a href="{{ route('admin.bookings.show', $task->booking) }}" style="font-weight: 700; color: var(--primary-hover); text-decoration: none;">
                                {{ $task->booking->booking_code }} <i class="fa-solid fa-up-right-from-square" style="font-size: 11px;"></i>
                            </a>
                        @else
                            <span style="color: var(--text-muted); font-style: italic;">ไม่มีข้อมูลใบจอง</span>
                        @endif
                    </div>
                    
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">สถานะงานติดตั้ง:</span>
                        @php
                            $statusClass = 'status-' . $task->status;
                            $statusName = 'รอเริ่มงาน';
                            switch($task->status) {
                                case 'waiting': $statusName = 'รอพนักงานเริ่มงาน'; break;
                                case 'started': $statusName = 'กำลังติดตั้ง'; break;
                                case 'completed': $statusName = 'ติดตั้งสำเร็จ'; break;
                                case 'problem': $statusName = 'พบปัญหาหน้างาน'; break;
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}" style="margin-top: 2px;">{{ $statusName }}</span>
                    </div>

                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">วันที่เข้าดำเนินการ:</span>
                        <strong>{{ $task->task_date->format('d/m/Y') }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">พนักงานรับผิดชอบ:</span>
                        <strong>{{ $task->staff ? $task->staff->name : 'ไม่ได้มอบหมาย' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">เวลาเริ่มงาน:</span>
                        <strong>{{ $task->started_at ? $task->started_at->format('H:i น.') : '-' }}</strong>
                    </div>

                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">เวลาส่งงานสำเร็จ:</span>
                        <strong>{{ $task->completed_at ? $task->completed_at->format('H:i น.') : '-' }}</strong>
                    </div>
                </div>

                @if ($task->status === 'problem' && $task->problem_note)
                    <div style="background-color: #FFF2E6; border: 2px solid #FFD8B3; border-radius: 16px; padding: 15px; margin-top: 20px;">
                        <strong style="color: #D35400; font-size: 14px; display: block; margin-bottom: 5px;">
                            <i class="fa-solid fa-triangle-exclamation"></i> รายละเอียดปัญหาที่พนักงานรายงาน:
                        </strong>
                        <span style="font-size: 14px; color: var(--text-dark);">{{ $task->problem_note }}</span>
                    </div>
                @endif
            </div>

            <!-- Installer Photos & Geolocation Info -->
            <div class="cute-card">
                <h3 class="cute-card-title">
                    <i class="fa-solid fa-images"></i> รูปภาพและรายละเอียดตำแหน่ง (GPS)
                </h3>

                @if ($task->photos->isEmpty())
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fa-solid fa-circle-nodes" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                        ยังไม่มีภาพถ่ายบันทึกสำหรับงานนี้
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                        @foreach ($task->photos as $photo)
                            <div style="border: 2px solid var(--border-cute); border-radius: 18px; overflow: hidden; background-color: var(--bg-page); display: flex; flex-direction: column;">
                                <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="รูปติดตั้ง" style="display:block;width:100%;">
                                    <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" style="width: 100%; height: 150px; object-fit: cover; display: block;" alt="รูปติดตั้ง">
                                </button>
                                <div style="padding: 12px; font-size: 13px; display: flex; flex-direction: column; gap: 4px; flex: 1;">
                                    <strong style="color: var(--text-dark);">
                                        @if($photo->photo_type === 'lot_number') 📝 รูปป้ายเลขแผง
                                        @elseif($photo->photo_type === 'before') 🛠️ ก่อนเริ่มงาน
                                        @elseif($photo->photo_type === 'after') ✅ งานเสร็จเรียบร้อย
                                        @elseif($photo->photo_type === 'problem') ⚠️ พบปัญหา
                                        @endif
                                    </strong>

                                    @if($photo->photo_type === 'lot_number')
                                        <div style="font-size: 12px; font-weight: 800; color: @if($photo->ocr_status === 'approved') #1E7E34 @elseif($photo->ocr_status === 'pending_review') #856404 @else #D35400 @endif;">
                                            ตรวจเลขล็อต:
                                            @if($photo->ocr_status === 'approved') ผ่าน
                                            @elseif($photo->ocr_status === 'pending_review') รอตรวจ
                                            @elseif($photo->ocr_status === 'rejected') ไม่ผ่าน
                                            @else ยังไม่ตรวจ
                                            @endif
                                        </div>
                                        @if($photo->ocr_text)
                                            <div style="color: var(--text-muted); font-size: 11px; word-break: break-word;">{{ $photo->ocr_text }}</div>
                                        @endif
                                    @endif
                                    
                                    @if($photo->taken_at)
                                        <div style="color: var(--text-muted); font-size: 11px;">เวลา: {{ $photo->taken_at->format('d/m/Y H:i น.') }}</div>
                                    @endif

                                    @if($photo->latitude && $photo->longitude)
                                        <div style="margin-top: 5px; padding-top: 5px; border-top: 1px dashed var(--border-cute); font-size: 11px;">
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $photo->latitude }},{{ $photo->longitude }}" target="_blank" style="color: var(--secondary); text-decoration: none; font-weight: 700;">
                                                <i class="fa-solid fa-location-dot"></i> ดูพิกัดบนแผนที่ Google <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                                            </a>
                                            <div style="color: var(--text-muted); font-size: 10px;">({{ round($photo->latitude, 5) }}, {{ round($photo->longitude, 5) }})</div>
                                        </div>
                                    @else
                                        <div style="color: var(--text-muted); font-size: 11px; margin-top: 5px; font-style: italic;">ไม่ได้ระบุพิกัดจีพีเอส</div>
                                    @endif

                                    @if($photo->note)
                                        <div style="background-color: var(--bg-card); border-radius: 8px; padding: 6px; font-size: 11px; border: 1px dashed var(--border-cute); margin-top: 5px;">
                                            {{ $photo->note }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Status log -->
        <div>
            <div class="cute-card">
                <h3 class="cute-card-title" style="font-size: 18px;">
                    <i class="fa-solid fa-list-check"></i> บันทึกประวัติงานติดตั้ง
                </h3>
                
                @if ($logs->isEmpty())
                    <span style="color: var(--text-muted); font-size: 13px; font-style: italic;">ไม่มีข้อมูลบันทึก</span>
                @else
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                        @foreach ($logs as $log)
                            <div style="border-left: 3px solid var(--primary); padding-left: 12px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <strong>สถานะ: 
                                        @php
                                            $stName = $log->new_status;
                                            switch($log->new_status) {
                                                case 'waiting': $stName = 'มอบหมายพนักงาน'; break;
                                                case 'started': $stName = 'เริ่มติดตั้ง'; break;
                                                case 'completed': $stName = 'ติดตั้งสำเร็จ'; break;
                                                case 'problem': $stName = 'มีปัญหาการทำแผง'; break;
                                            }
                                            echo $stName;
                                        @endphp
                                    </strong>
                                    <small style="color: var(--text-muted);">{{ $log->created_at->format('H:i น.') }}</small>
                                </div>
                                <div style="color: var(--text-muted); font-size: 12px; margin-top: 2px;">
                                    {{ $log->note }}
                                </div>
                                <div style="color: var(--text-muted); font-size: 11px; margin-top: 2px;">
                                    วันที่: {{ $log->created_at->format('d/m/Y') }} &bull; โดย: {{ $log->changedBy ? $log->changedBy->name : 'ระบบ' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('components.image-lightbox')
@endsection
