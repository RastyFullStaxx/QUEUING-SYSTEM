<template>
  <div class="w-full py-12 px-10">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-xs uppercase tracking-[0.35em] text-[#6B7280]">Resident</p>
        <h1 class="text-4xl font-semibold text-[#0B2C6F]">Profile</h1>
      </div>
      <router-link class="text-sm font-semibold text-slate-900 bg-white/80 px-4 py-2 rounded-full border" to="/dashboard">
        Back to dashboard
      </router-link>
    </div>
    <div class="mt-10 grid lg:grid-cols-[0.9fr,1.1fr] gap-6">
      <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6 space-y-6">
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-2xl bg-[#0B2C6F] text-white flex items-center justify-center">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c1.8-4 6-6 8-6s6.2 2 8 6" />
            </svg>
          </div>
          <div>
            <p class="text-sm text-[#6B7280]">Resident card</p>
            <p class="text-xl font-semibold">Resident Name</p>
          </div>
        </div>
        <div class="rounded-2xl bg-[#F3F4F6] border border-[#E5E7EB] p-4 text-sm text-[#6B7280]">
          Status: <span class="font-semibold text-[#F2C300]">Pending</span>
        </div>
        <div class="space-y-3 text-sm text-[#0B2C6F]">
          <div class="flex items-center justify-between">
            <span>Full name</span>
            <span class="font-semibold">Resident Name</span>
          </div>
          <div class="flex items-center justify-between">
            <span>Email</span>
            <span class="font-semibold">resident@email.com</span>
          </div>
          <div class="flex items-center justify-between">
            <span>Contact</span>
            <span class="font-semibold">+63 900 000 0000</span>
          </div>
        </div>
        <button class="w-full bg-[#0B2C6F] text-white py-3 rounded-2xl text-sm">Edit profile</button>
      </div>
      <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6">
          <h2 class="text-lg font-semibold">Verification</h2>
          <p class="mt-2 text-sm text-[#6B7280]">Upload a valid ID to complete verification.</p>
          <div class="mt-6 border-2 border-dashed rounded-2xl p-6 text-center text-sm text-[#6B7280]">
            Drop your ID file here or click to upload.
          </div>
        </div>
        <div class="bg-white rounded-3xl border border-[#E5E7EB] p-6">
          <h2 class="text-lg font-semibold">Barangay services</h2>
          <p class="mt-2 text-sm text-[#6B7280]">Choose services after approval to generate your queue ticket.</p>
          <div class="mt-4 flex flex-wrap gap-3">
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Clearance</span>
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Business Permit</span>
            <span class="px-3 py-1 rounded-full text-xs bg-[#F3F4F6] text-[#0B2C6F] border border-[#E5E7EB]">Residency</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
