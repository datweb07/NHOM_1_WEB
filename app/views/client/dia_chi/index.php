<?php
require_once dirname(__DIR__, 3) . '/models/entities/DiaChi.php';
require_once dirname(__DIR__, 3) . '/core/Session.php';

\App\Core\Session::start();
$userId = $_SESSION['user_id'] ?? null;

$diaChiModel = new DiaChi();
$danhSachDiaChi = [];

if ($userId) {
    $danhSachDiaChi = $diaChiModel->layDanhSachTheoUser((int)$userId);
}
?>

<div class="profile-content-box">
    <div class="profile-content-header d-flex justify-content-between align-items-center">
        <div>
            <h2>Sổ địa chỉ</h2>
            <p>Quản lý địa chỉ nhận hàng của bạn</p>
        </div>
    </div>

    <div class="card bg-light border-0 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3 fs-6">Thêm địa chỉ mới</h5>
            <form action="/khach-hang/them-dia-chi" method="POST" id="formDiaChi">
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="ho_ten_nguoi_nhan" placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium small">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="sdt_nguoi_nhan" placeholder="Nhập số điện thoại" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <select class="form-select" id="tinhThanh" name="tinh_thanh" required>
                            <option value="" selected disabled>Chọn Tỉnh/Thành</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Quận/Huyện <span class="text-danger">*</span></label>
                        <select class="form-select" id="quanHuyen" name="quan_huyen" required disabled>
                            <option value="" selected disabled>Chọn Quận/Huyện</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium small">Phường/Xã <span class="text-danger">*</span></label>
                        <select class="form-select" id="phuongXa" name="phuong_xa" required disabled>
                            <option value="" selected disabled>Chọn Phường/Xã</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium small">Địa chỉ cụ thể (Số nhà, tên đường) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="dia_chi_cu_the" placeholder="Ví dụ: Số 1, Đường Lê Duẩn" required>
                </div>

                <div class="d-flex align-items-center mb-4">
    <input type="checkbox" name="is_mac_dinh" value="1" id="checkMacDinh" style="width: 16px; height: 16px; cursor: pointer; margin: 0; accent-color: #cb1c22;">
    <label class="small ms-2 user-select-none mb-0" for="checkMacDinh" style="cursor: pointer;">
        Đặt làm địa chỉ mặc định
    </label>
</div>

                <div class="text-end">
                    <button type="submit" class="btn-submit"><i class="bi bi-plus-circle me-1"></i> Thêm địa chỉ</button>
                </div>
            </form>
        </div>
    </div>

    <div class="saved-addresses">
        <h5 class="fw-bold mb-3 fs-6">Địa chỉ đã lưu</h5>
        
        <?php if (empty($danhSachDiaChi)): ?>
            <div class="text-center py-4 text-muted border rounded" style="background-color: #fafafa; border-style: dashed !important;">
                <i class="bi bi-geo me-2"></i>Bạn chưa lưu địa chỉ nào.
            </div>
        <?php else: ?>
            <?php foreach ($danhSachDiaChi as $diaChi): ?>
    <div class="address-card card mb-3 <?php echo $diaChi['mac_dinh'] ? 'border-primary' : ''; ?>">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <?php if ($diaChi['mac_dinh']): ?>
                        <span class="badge bg-primary mb-2">Mặc định</span>
                    <?php endif; ?>
                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($diaChi['ten_nguoi_nhan']); ?></h6>
                    <p class="mb-1 text-muted small">
                        <i class="bi bi-telephone me-1"></i>
                        <?php echo htmlspecialchars($diaChi['sdt_nhan']); ?>
                    </p>
                    <p class="mb-0 small">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?php echo htmlspecialchars($diaChi['so_nha_duong']); ?>, 
                        <?php echo htmlspecialchars($diaChi['phuong_xa']); ?>, 
                        <?php echo htmlspecialchars($diaChi['quan_huyen']); ?>, 
                        <?php echo htmlspecialchars($diaChi['tinh_thanh']); ?>
                    </p>
                </div>
                <div class="btn-group-vertical btn-group-sm ms-3">
                    <?php if (!$diaChi['mac_dinh']): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="setDefault(<?php echo $diaChi['id']; ?>)">
                            <i class="bi bi-star"></i> Đặt mặc định
                        </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                            onclick="editAddress(<?php echo $diaChi['id']; ?>)">
                        <i class="bi bi-pencil"></i> Sửa
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            onclick="deleteAddress(<?php echo $diaChi['id']; ?>)">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.address-card {
    transition: box-shadow 0.3s ease;
}
.address-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.address-card.border-primary {
    border-width: 2px !important;
}
</style>

<script>
// Address management JavaScript functions
const addressesData = <?php echo json_encode($danhSachDiaChi); ?>;

// Edit address - populate form with existing data
function editAddress(id) {
    const address = addressesData.find(a => a.id == id);
    if (!address) return;
    
    // Populate form fields với đúng tên cột
    document.querySelector('input[name="ho_ten_nguoi_nhan"]').value = address.ten_nguoi_nhan;
    document.querySelector('input[name="sdt_nguoi_nhan"]').value = address.sdt_nhan;
    document.querySelector('input[name="dia_chi_cu_the"]').value = address.so_nha_duong;
    document.getElementById('checkMacDinh').checked = address.mac_dinh == 1;
    
    // Cập nhật form action và thêm hidden id
    const form = document.getElementById('formDiaChi');
    form.action = '/khach-hang/cap-nhat-dia-chi';
    
    let idInput = form.querySelector('input[name="id"]');
    if (!idInput) {
        idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        form.appendChild(idInput);
    }
    idInput.value = id;
    
    // Đổi text nút submit
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Cập nhật địa chỉ';
    
    // Scroll lên form
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Chọn lại các select (tỉnh/huyện/xã) dựa trên giá trị hiện có
    setTimeout(() => {
        setSelectValue('tinhThanh', address.tinh_thanh);
        document.getElementById('tinhThanh').dispatchEvent(new Event('change'));
        
        setTimeout(() => {
            setSelectValue('quanHuyen', address.quan_huyen);
            document.getElementById('quanHuyen').dispatchEvent(new Event('change'));
            
            setTimeout(() => {
                setSelectValue('phuongXa', address.phuong_xa);
            }, 300);
        }, 300);
    }, 300);
}

// Helper function to set select value by text
function setSelectValue(selectId, value) {
    const select = document.getElementById(selectId);
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === value) {
            select.selectedIndex = i;
            break;
        }
    }
}

// Delete address with confirmation
function deleteAddress(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/khach-hang/xoa-dia-chi';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = id;
    form.appendChild(idInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Set address as default
function setDefault(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/khach-hang/dat-mac-dinh';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = id;
    form.appendChild(idInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Reset form to add mode
function resetForm() {
    const form = document.getElementById('formDiaChi');
    form.reset();
    form.action = '/khach-hang/them-dia-chi';
    
    // Remove hidden ID field if exists
    const idInput = form.querySelector('input[name="id"]');
    if (idInput) {
        idInput.remove();
    }
    
    // Reset submit button text
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Thêm địa chỉ';
}
</script>

<script>
// ==========================================
// TÍCH HỢP DATA HÀNH CHÍNH VIỆT NAM (dvhcvn)
// ==========================================
document.addEventListener("DOMContentLoaded", function() {
    const tinhThanhSelect = document.getElementById('tinhThanh');
    const quanHuyenSelect = document.getElementById('quanHuyen');
    const phuongXaSelect = document.getElementById('phuongXa');
    let vnData = [];

    const dataUrl = 'https://raw.githubusercontent.com/daohoangson/dvhcvn/master/data/dvhcvn.json';

    fetch(dataUrl)
        .then(response => response.json())
        .then(data => {
            vnData = data.data; 
            vnData.forEach(tinh => {
                const option = document.createElement('option');
                option.value = tinh.name; 
                option.dataset.id = tinh.level1_id; 
                option.textContent = tinh.name;
                tinhThanhSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Lỗi khi tải dữ liệu hành chính:', error);
            tinhThanhSelect.innerHTML = '<option disabled>Không thể tải dữ liệu</option>';
        });

    tinhThanhSelect.addEventListener('change', function() {
        const selectedId = this.options[this.selectedIndex].dataset.id;
        
        quanHuyenSelect.innerHTML = '<option value="" selected disabled>Chọn Quận/Huyện</option>';
        phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
        phuongXaSelect.disabled = true;

        if (selectedId) {
            const tinhData = vnData.find(t => t.level1_id === selectedId);
            if (tinhData && tinhData.level2s) {
                tinhData.level2s.forEach(quan => {
                    const option = document.createElement('option');
                    option.value = quan.name; 
                    option.dataset.id = quan.level2_id;
                    option.textContent = quan.name;
                    quanHuyenSelect.appendChild(option);
                });
                quanHuyenSelect.disabled = false;
            }
        }
    });

    quanHuyenSelect.addEventListener('change', function() {
        const selectedTinhId = tinhThanhSelect.options[tinhThanhSelect.selectedIndex].dataset.id;
        const selectedQuanId = this.options[this.selectedIndex].dataset.id;

        phuongXaSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';

        if (selectedTinhId && selectedQuanId) {
            const tinhData = vnData.find(t => t.level1_id === selectedTinhId);
            const quanData = tinhData.level2s.find(q => q.level2_id === selectedQuanId);

            if (quanData && quanData.level3s) {
                quanData.level3s.forEach(phuong => {
                    const option = document.createElement('option');
                    option.value = phuong.name;
                    option.dataset.id = phuong.level3_id;
                    option.textContent = phuong.name;
                    phuongXaSelect.appendChild(option);
                });
                phuongXaSelect.disabled = false;
            }
        }
    });
});
</script>