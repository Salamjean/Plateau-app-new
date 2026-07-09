import re
import os

files = [
    'resources/views/user/naissance/simple/create.blade.php',
    'resources/views/user/mariage/simple/create.blade.php',
    'resources/views/user/deces/simple/create.blade.php'
]

btn_html = """<button type="button" id="btn-pay-wave" class="payment-method-btn active-payment" style="background: #eff6ff; border: 2px solid #1e3a8a; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('wave')">
                                                <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave" style="height: 30px; object-fit: contain;">
                                            </button>
                                            <button type="button" id="btn-pay-tresorpay" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('tresorpay')">
                                                <i class="fas fa-wallet" style="font-size: 25px; color: #e35205;"></i>
                                            </button>"""

tresorpay_logic = """if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'tresorpay') {
                activeBtn.style.border = '2px solid #e35205';
                activeBtn.style.backgroundColor = '#fff5f0';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro TrésorMoney (ex: 0767664010)';
            } else if (method === 'mtn') {"""

old_logic = """if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'mtn') {"""

for f in files:
    if not os.path.exists(f):
        print(f"File not found: {f}")
        continue
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Replace wave button with wave + tresorpay
    content = re.sub(r'<button type="button" id="btn-pay-wave"[^>]*>\s*<img[^>]*Wave\.png[^>]*>\s*</button>', btn_html, content)
    
    content = content.replace("mtn_number: payment_method === 'mtn' ? payment_number : '',", "mtn_number: (payment_method === 'mtn' || payment_method === 'tresorpay') ? payment_number : '',")
    
    content = content.replace("if (method !== 'wave' && method !== 'mtn') return;", "if (method !== 'wave' && method !== 'mtn' && method !== 'tresorpay') return;")
    
    content = content.replace(old_logic, tresorpay_logic)
    
    content = content.replace("grid-template-columns: 1fr 1fr; gap: 8px;", "grid-template-columns: 1fr 1fr 1fr; gap: 8px;")

    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
    print(f"Updated {f}")
