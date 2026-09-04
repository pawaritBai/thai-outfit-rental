<div class="mb-4">
    <label class="block font-medium text-gray-700">ชื่อที่อยู่:</label>
    <input type="text" name="AddressName" class="form-input w-full border-gray-300 rounded-md mt-1"
           value="{{ old('AddressName', $data->AddressName ?? '') }}" required>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block font-medium text-gray-700">จังหวัด:</label>
        <select id="province" name="Province" class="form-input w-full border-gray-300 rounded-md mt-1" required>
            <option value="">-- เลือกจังหวัด --</option>
        </select>
    </div>
    <div>
        <label class="block font-medium text-gray-700">อำเภอ:</label>
        <select id="district" name="District" class="form-input w-full border-gray-300 rounded-md mt-1" required>
            <option value="">-- เลือกอำเภอ --</option>
        </select>
    </div>
    <div>
        <label class="block font-medium text-gray-700">ตำบล:</label>
        <select id="subdistrict" name="Subdistrict" class="form-input w-full border-gray-300 rounded-md mt-1" required>
            <option value="">-- เลือกตำบล --</option>
        </select>
    </div>
    <div>
        <label class="block font-medium text-gray-700">รหัสไปรษณีย์:</label>
        <input type="text" id="postal_code" name="PostalCode" class="form-input w-full border-gray-300 bg-gray-100 rounded-md mt-1" 
               value="{{ old('PostalCode', $data->address->PostalCode ?? '') }}" readonly required>
    </div>
</div>

<div class="mb-4">
    <label class="block font-medium text-gray-700">บ้านเลขที่:</label>
    <input type="text" name="HouseNumber" class="form-input w-full border-gray-300 rounded-md mt-1"
           value="{{ old('HouseNumber', $data->address->HouseNumber ?? '') }}" required>
</div>

<div class="mb-6">
    <label class="block font-medium text-gray-700">ถนน / ซอย (ถ้ามี):</label>
    <input type="text" name="Street" class="form-input w-full border-gray-300 rounded-md mt-1"
           value="{{ old('Street', $data->address->Street ?? '') }}">
</div>

<!-- JS Section -->
@push('scripts')
<script>
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const subdistrictSelect = document.getElementById('subdistrict');
    const postalCodeInput = document.getElementById('postal_code');

    let addressData = [];

    async function loadAddressData() {
        try {
            const res = await fetch('https://raw.githubusercontent.com/kongvut/thai-province-data/master/api_province_with_amphure_tambon.json');
            addressData = await res.json();

            addressData.forEach(province => {
                const opt = document.createElement('option');
                opt.value = province.name_th;
                opt.textContent = province.name_th;
                provinceSelect.appendChild(opt);
            });

            // ตั้งค่า default หากมีค่าเดิม (สำหรับหน้า edit)
            const oldProvince = "{{ old('Province', $data->address->Province ?? '') }}";
            const oldDistrict = "{{ old('District', $data->address->District ?? '') }}";
            const oldSubdistrict = "{{ old('Subdistrict', $data->address->Subdistrict ?? '') }}";

            if (oldProvince) {
                provinceSelect.value = oldProvince;
                populateDistricts(oldProvince, oldDistrict, oldSubdistrict);
            }

        } catch (err) {
            alert('โหลดข้อมูลที่อยู่ไม่สำเร็จ');
        }
    }

    function populateDistricts(provinceName, selectedDistrict = '', selectedSubdistrict = '') {
        districtSelect.innerHTML = '<option value="">-- เลือกอำเภอ --</option>';
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';

        const province = addressData.find(p => p.name_th === provinceName);
        province?.amphure?.forEach(district => {
            const opt = document.createElement('option');
            opt.value = district.name_th;
            opt.textContent = district.name_th;
            districtSelect.appendChild(opt);
        });

        if (selectedDistrict) {
            districtSelect.value = selectedDistrict;
            populateSubdistricts(provinceName, selectedDistrict, selectedSubdistrict);
        }
    }

    function populateSubdistricts(provinceName, districtName, selectedSubdistrict = '') {
        subdistrictSelect.innerHTML = '<option value="">-- เลือกตำบล --</option>';
        postalCodeInput.value = '';

        const province = addressData.find(p => p.name_th === provinceName);
        const district = province?.amphure?.find(d => d.name_th === districtName);

        district?.tambon?.forEach(sub => {
            const opt = document.createElement('option');
            opt.value = sub.name_th;
            opt.textContent = sub.name_th;
            opt.dataset.zip = sub.zip_code;
            subdistrictSelect.appendChild(opt);
        });

        if (selectedSubdistrict) {
            subdistrictSelect.value = selectedSubdistrict;
            const selected = subdistrictSelect.querySelector(`[value="${selectedSubdistrict}"]`);
            postalCodeInput.value = selected?.dataset?.zip || '';
        }
    }

    provinceSelect.addEventListener('change', () => {
        populateDistricts(provinceSelect.value);
    });

    districtSelect.addEventListener('change', () => {
        populateSubdistricts(provinceSelect.value, districtSelect.value);
    });

    subdistrictSelect.addEventListener('change', () => {
        const zip = subdistrictSelect.options[subdistrictSelect.selectedIndex]?.dataset?.zip;
        postalCodeInput.value = zip || '';
    });

    document.addEventListener('DOMContentLoaded', loadAddressData);
</script>
@endpush
