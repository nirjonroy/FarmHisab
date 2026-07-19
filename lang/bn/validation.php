<?php

return [
    'accepted' => ':attribute গ্রহণ করতে হবে।',
    'boolean' => ':attribute সত্য বা মিথ্যা হতে হবে।',
    'confirmed' => ':attribute নিশ্চিতকরণ মিলছে না।',
    'email' => ':attribute সঠিক ইমেইল ঠিকানা হতে হবে।',
    'exists' => 'নির্বাচিত :attribute সঠিক নয়।',
    'integer' => ':attribute পূর্ণসংখ্যা হতে হবে।',
    'max' => [
        'numeric' => ':attribute :max এর বেশি হতে পারবে না।',
        'file' => ':attribute :max কিলোবাইটের বেশি হতে পারবে না।',
        'string' => ':attribute :max অক্ষরের বেশি হতে পারবে না।',
        'array' => ':attribute-এ :maxটির বেশি আইটেম থাকতে পারবে না।',
    ],
    'min' => [
        'numeric' => ':attribute কমপক্ষে :min হতে হবে।',
        'file' => ':attribute কমপক্ষে :min কিলোবাইট হতে হবে।',
        'string' => ':attribute কমপক্ষে :min অক্ষর হতে হবে।',
        'array' => ':attribute-এ কমপক্ষে :minটি আইটেম থাকতে হবে।',
    ],
    'nullable' => ':attribute খালি থাকতে পারে।',
    'required' => ':attribute আবশ্যক।',
    'string' => ':attribute টেক্সট হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে ব্যবহৃত হয়েছে।',

    'custom' => [
        'parent_id' => [
            'top_level_parent' => 'শুধু টপ-লেভেল ক্যাটাগরি প্যারেন্ট হিসেবে নির্বাচন করা যাবে।',
            'self_parent' => 'ক্যাটাগরি নিজের প্যারেন্ট হতে পারে না।',
            'parent_with_children' => 'চাইল্ড ক্যাটাগরি আছে এমন ক্যাটাগরিকে অন্য প্যারেন্টের অধীনে নেওয়া যাবে না।',
        ],
    ],

    'attributes' => [
        'name' => 'নাম',
        'email' => 'ইমেইল',
        'password' => 'পাসওয়ার্ড',
        'role' => 'ভূমিকা',
        'is_active' => 'সক্রিয় অবস্থা',
        'code' => 'কোড',
        'phone' => 'ফোন',
        'address' => 'ঠিকানা',
        'district' => 'জেলা',
        'upazila' => 'উপজেলা',
        'union_name' => 'ইউনিয়ন',
        'description' => 'বিবরণ',
        'farm_id' => 'ফার্ম',
        'capacity' => 'ধারণক্ষমতা',
        'parent_id' => 'প্যারেন্ট ক্যাটাগরি',
        'slug' => 'স্লাগ',
        'icon' => 'আইকন',
        'sort_order' => 'সাজানোর ক্রম',
    ],
];
