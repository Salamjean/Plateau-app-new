with open(".env", "rb") as f:
    text = f.read().decode("utf-16le")

with open(".env", "w", encoding="utf-8") as f:
    f.write(text.replace('WAVE_WEBHOOK_SECRET=wave_ci_WHS_y4rag0dpae6ythp9c3hcq81snc6dtf1zjzvxhm1n9vxw6bpz60dg', '\nWAVE_WEBHOOK_SECRET=wave_ci_WHS_y4rag0dpae6ythp9c3hcq81snc6dtf1zjzvxhm1n9vxw6bpz60dg'))

print("done")
