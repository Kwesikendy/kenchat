<?php
session_start(); // start session FIRST before anything else
include('../backend/config.php');

// Check if user is logged in first
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in. Please log in first.");
}

$uid = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE id = '$uid'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

// Now safe to use user id
$userId = $_SESSION['user_id']; 

// Query for Received Requests
$receivedSql = "SELECT f.id AS friend_id, u.name, u.email
                FROM friends f
                JOIN user u ON u.id = f.f1_id
                WHERE f.f2_id = $userId AND f.status = 'pending'";
$receivedResult = mysqli_query($conn, $receivedSql);

// Query for Sent Requests
$sentSql = "SELECT f.id AS friend_id, u.name, u.email, f.status
            FROM friends f
            JOIN user u ON u.id = f.f2_id
            WHERE f.f1_id = $userId";
$sentResult = mysqli_query($conn, $sentSql);

// Query for Friends (approved status)
$friendsSql = "SELECT u.name, u.email
               FROM friends f
               JOIN user u ON (u.id = CASE WHEN f.f1_id = $userId THEN f.f2_id ELSE f.f1_id END)
               WHERE (f.f1_id = $userId OR f.f2_id = $userId) AND f.status = 'approved'";
$friendsResult = mysqli_query($conn, $friendsSql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat App</title>
  <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet"> -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans&family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');


    body {
      background: #201C2F;
    }
  </style>
  <!-- <script>
    tailwind.config = {
      theme: {
        fontFamily: {
          sans: ['Inter'],
        },
        extend: {
          colors: {
            'primary-bg': '#201C2F',
            light: '#343143',
          },
        },
      },
      plugins: [],
    }
  </script> -->
  <script src="../backend/script.js"></script></head>


<body>
  
  <div class="flex min-h-screen w-full bg-primary-bg">
    <div class="relative w-[370px] border-r border-r-slate-700 px-4 py-4 ">
      <div class="relative flex items-center">
        
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute ml-2 h-5 w-5 text-slate-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="text" placeholder="Search" class="w-full rounded-md border-none bg-[#343143] py-2 pl-9 pr-3 text-slate-200 focus:outline-none" />
      </div>
      <a href = "javascript:void(0)" id = "friends-btn" class="flex items-center justify-center text-xl gap-2 mt-3 py-2 px-3 bg-indigo-600 hover:bg-indigo-500 rounded-md px-2 text-center text-slate-200 cursor-pointer transition-colors duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 space">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>

        <span class="text-sm font-medium cursor-pointer">Friend Request</span>
      </a>

      <div class="mt-4 flex gap-2 overflow-hidden">
        <div class="flex flex-col items-center gap-1">
          <div class="h-[42px] w-[42px] rounded-full">
            <img src="https://picsum.photos/300/300" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <h2 class="text-xs text-slate-300">Me</h2>
        </div>
        <div class="flex flex-col items-center gap-1">
          <div class="h-[42px] w-[42px] rounded-full border-2 border-indigo-400">
            <img src="https://picsum.photos/500/500" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <h2 class="text-xs text-slate-300">Adrian</h2>
        </div>
      </div>


      <div class="mt-5 flex flex-col gap-2">
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/700/800" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Adrian Alvaro</h2>
            <p class="truncate text-sm text-slate-400">Why you are not replying</p>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <p class="grid h-4 w-4 place-content-center rounded-full bg-green-600 text-xs text-slate-200">4</p>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/1000/960" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Loress Bravo</h2>
            <p class="truncate text-sm text-slate-400">Let's meet today ?</p>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <svg class="h-4 w-4 fill-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M11.602 13.7599L13.014 15.1719L21.4795 6.7063L22.8938 8.12051L13.014 18.0003L6.65 11.6363L8.06421 10.2221L10.189 12.3469L11.6025 13.7594L11.602 13.7599ZM11.6037 10.9322L16.5563 5.97949L17.9666 7.38977L13.014 12.3424L11.6037 10.9322ZM8.77698 16.5873L7.36396 18.0003L1 11.6363L2.41421 10.2221L3.82723 11.6352L3.82604 11.6363L8.77698 16.5873Z"></path>
            </svg>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/900/300" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">James Rodrigo</h2>
            <p class="truncate text-sm text-slate-400">Will you watch the match today?</p>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <svg class="h-4 w-4 fill-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M10.0007 15.1709L19.1931 5.97852L20.6073 7.39273L10.0007 17.9993L3.63672 11.6354L5.05093 10.2212L10.0007 15.1709Z"></path>
            </svg>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/890/800" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Adrian Maron</h2>
            <p class="truncate text-sm text-slate-400">I can't Wait to meet you bro ?</p>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <svg class="h-4 w-4 fill-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M11.602 13.7599L13.014 15.1719L21.4795 6.7063L22.8938 8.12051L13.014 18.0003L6.65 11.6363L8.06421 10.2221L10.189 12.3469L11.6025 13.7594L11.602 13.7599ZM11.6037 10.9322L16.5563 5.97949L17.9666 7.38977L13.014 12.3424L11.6037 10.9322ZM8.77698 16.5873L7.36396 18.0003L1 11.6363L2.41421 10.2221L3.82723 11.6352L3.82604 11.6363L8.77698 16.5873Z"></path>
            </svg>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/700/700" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Arder Oghlo</h2>
            <p class="truncate text-sm text-slate-400">Let's meet today ?</p>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <p class="grid h-4 w-4 place-content-center rounded-full bg-green-600 text-xs text-slate-200">4</p>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/750/740" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Maria Lopez</h2>
            <div class="mt-1 flex items-center gap-1 text-sm text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-slate-300" viewBox="0 0 24 24">
                <path d="M5 7H7V17H5V7ZM1 10H3V14H1V10ZM9 2H11V20H9V2ZM13 4H15V22H13V4ZM17 7H19V17H17V7ZM21 10H23V14H21V10Z"></path>
              </svg>
              <span>Voice Message</span>
            </div>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <p class="grid h-4 w-4 place-content-center rounded-full bg-green-600 text-xs text-slate-200">1</p>
          </div>
        </button>
        <button class="flex items-center gap-2 rounded-md px-2 py-2 transition-colors duration-300 hover:bg-light">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/750/740" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
          <div class="overflow-hidden text-left">
            <h2 class="truncate text-sm font-medium text-slate-200">Nariman Joe</h2>
            <div class="mt-1 flex items-center gap-1 text-sm text-slate-400">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-slate-300" viewBox="0 0 24 24">
                <path d="M5 7H7V17H5V7ZM1 10H3V14H1V10ZM9 2H11V20H9V2ZM13 4H15V22H13V4ZM17 7H19V17H17V7ZM21 10H23V14H21V10Z"></path>
              </svg>
              <span>Voice Message</span>
            </div>
          </div>
          <div class="ml-auto flex flex-col items-end gap-1">
            <p class="text-xs text-slate-400">11:30</p>
            <svg class="h-4 w-4 fill-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M11.602 13.7599L13.014 15.1719L21.4795 6.7063L22.8938 8.12051L13.014 18.0003L6.65 11.6363L8.06421 10.2221L10.189 12.3469L11.6025 13.7594L11.602 13.7599ZM11.6037 10.9322L16.5563 5.97949L17.9666 7.38977L13.014 12.3424L11.6037 10.9322ZM8.77698 16.5873L7.36396 18.0003L1 11.6363L2.41421 10.2221L3.82723 11.6352L3.82604 11.6363L8.77698 16.5873Z"></path>
            </svg>
          </div>
        </button>
      </div>
      <div class="border-t-2 border-white rounded-lg flex flex-row absolute inset-x-0 bottom-0 p-2 space-x-4">
        <!-- Profile/avatar div -->
        <div class="my-auto">
          <div class="h-[42px] w-[42px] shrink-0 rounded-full">
            <img src="https://picsum.photos/700/800" class="h-full w-full rounded-full object-cover" alt="" />
          </div>
        </div>
        <!-- Name of person logged in and email -->
        <div class="flex flex-col text-white my-auto">
          <h1><?php echo $row['name']; ?></h1>
          <h1><?php echo $row['email']; ?></h1>
        </div>
        <!-- Logout button -->
        <a href="logout.php">
          <div class="my-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-red-500">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
            </svg>

          </div>
        </a>
        <!-- Logout button end -->

      </div>
    </div>
    <div class="flex-1 relative">


      <div class="flex items-center gap-2 px-3 py-2 border-b border-b-slate-700">
        <div class="h-[42px] w-[42px] shrink-0 rounded-full">
          <img src="https://picsum.photos/750/740" class="h-full w-full rounded-full object-cover" alt="" />
        </div>
        <div>
          <h2 class="text-base text-slate-200">James Rodrigo</h2>
          <p class="text-xs text-slate-400">Online 3 min ago</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 shrink-0 text-slate-400">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" class="ml-3 h-6 w-6 fill-slate-400" viewBox="0 0 24 24">
            <path d="M4.5 10.5C3.675 10.5 3 11.175 3 12C3 12.825 3.675 13.5 4.5 13.5C5.325 13.5 6 12.825 6 12C6 11.175 5.325 10.5 4.5 10.5ZM19.5 10.5C18.675 10.5 18 11.175 18 12C18 12.825 18.675 13.5 19.5 13.5C20.325 13.5 21 12.825 21 12C21 11.175 20.325 10.5 19.5 10.5ZM12 10.5C11.175 10.5 10.5 11.175 10.5 12C10.5 12.825 11.175 13.5 12 13.5C12.825 13.5 13.5 12.825 13.5 12C13.5 11.175 12.825 10.5 12 10.5Z"></path>
          </svg>
          <div class="flex">
            <div class="ml-2 h-[35px] w-[35px] shrink-0 rounded-full">
              <img src="https://picsum.photos/754/740" class="h-full w-full rounded-full object-cover" alt="" />
            </div>
            <div class="-ml-5 h-[35px] w-[35px] shrink-0 rounded-full">
              <img src="https://picsum.photos/750/710" class="h-full w-full rounded-full object-cover" alt="" />
            </div>
            <div class="-ml-5 h-[35px] w-[35px] shrink-0 rounded-full">
              <img src="https://picsum.photos/720/740" class="h-full w-full rounded-full object-cover" alt="" />
            </div>
            <div class="-ml-5 grid h-[35px] w-[35px] shrink-0 place-content-center rounded-full border border-slate-700 bg-light/50 text-sm text-slate-100">+8</div>
          </div>
        </div>
      </div>
      <div id="chat-messages" class="flex flex-col gap-2 overflow-y-auto h-[400px] px-4 py-2 bg-[#201C2F]">
  <!-- Messages will be dynamically appended here -->
</div>

<div class="absolute bottom-0 left-0 right-0 z-10 flex items-center gap-3 border-t border-t-slate-700 bg-[#201C2F] px-4 py-3">
  <input id="chat-input" type="text" placeholder="Type a message..." class="flex-1 h-8 rounded-full border-none bg-[#343143] px-4 text-slate-200 focus:outline-none" />
  <button id="send-message" class="p-2 rounded-full bg-indigo-600 hover:bg-indigo-700">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L11 13" />
      <path stroke-linecap="round" stroke-linejoin="round" d="M22 2L15 22L11 13L2 9L22 2Z" />
    </svg>
  </button>
</div>
      
        <button class="p-2 rounded-full bg-light hover:bg-light/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v9.75M3 16.5l4.5-4.5c.414-.414 1.086-.414 1.5 0l3 3 6-6c.414-.414 1.086-.414 1.5 0L21 16.5M3 16.5h18M12 12l.01-.01" />
          </svg>
        </button>
        <input type="file" id="file-upload" class="hidden" />
      </div>
    </div>

  </div>

  </div>
  </div>
  <!--modal-->
  <!-- Search Modal -->
   <div id = "search-modal" class = "hidden fixed inset-0 z-50 bg-transparent bg-opacity-50 flex items-center justify-center overflow-auto">
    <div class = "bg-[#343143] rounded-xl w-4/5 max-w-2xl item-center justify-center">
      <span class = "text-white float-right text-3xl font-bold cursor-pointer hover:text-red-500 p-4 close-search-modal">&times;</span>
      <h2 class = "text-xl font-semibold text-slate-200 p-4 text-center">Find Friends</h2>


      <div class="relative flex items-center p-2">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="absolute left-4 h-5 w-5 text-slate-400">
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
  </svg>
  <input type="text" placeholder="Search by Email..." id="email-search" class="w-full rounded-md border border-slate-700 bg-[#201C2F] py-2 pl-12 pr-3 text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
</div>

<div id = "search-results" class = "max-h-[300px] overflow-y-auto p-4">
  </div>
  </div>

</div>
  <!-- Friend Requests modal -->
  <div id = "friends-modal" class = "hidden fixed inset-0 z-50 bg-transparent bg-opacity-50 flex items-center justify-center overflow-auto">
    <div class = "bg-[#343143] rounded-xl w-4/5 max-w-2xl">
      <span class = "text-white float-right text-3xl font-bold cursor-pointer hover:text-red-500 p-4" data-model= "friends-modal">&times;</span>
      <h2 class = "text-xl font-semibold text-slate-200 p-4 text-center">Friend requests and Friends</h2>
      <div class = "flex border-b border-white mb-4">
        <div data-tab = "received" class = "tab active cursor-pointer border-b-2 py-2 px-5 border-indigo-600 text-white">Received Requests</div>
        <div data-tab = "sent" class = "tab cursor-pointer py-2 px-5 text-gray-400 hover:text-white">Sent Requests</div>
        <div data-tab = "friends" class = "tab cursor-pointer py-2 px-5 text-gray-400 hover:text-white">Friends</div>
      </div>

      <div class="tab-content block max-h-350px overflow-y-auto" id="received">
    <h1 class="font-bold text-white">Friend Requests Received</h1>
    <?php if (mysqli_num_rows($receivedResult) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($receivedResult)): ?>
            <div class="friend-request p-4 bg-gray-800 rounded-lg mb-4 flex items-center">
                <!-- Displaying Profile Picture -->
                <img src="https://randomuser.me/api/portraits/men/<?php echo rand(1, 99); ?>.jpg" alt="Profile Pic" class="w-16 h-16 rounded-full mr-4">
                
                <!-- Displaying Name and Email -->
                <div>
                    <p class="text-white font-bold"><?php echo htmlspecialchars($row['name']); ?></p>
                    <p class="text-gray-300"><?php echo htmlspecialchars($row['email']); ?></p>
                </div>
                
                <form method="POST" action="acceptFriendRequest.php" class="ml-auto">
                    <input type="hidden" name="friend_id" value="<?php echo intval($row['friend_id']); ?>">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded">
                        Accept
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-400">No received friend requests.</p>
    <?php endif; ?>
</div>

<div class="tab-content block max-h-350px overflow-y-auto" id="sent">
    <h1 class="font-bold text-white">Friend Requests Sent</h1>
    <?php if (mysqli_num_rows($sentResult) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($sentResult)): ?>
            <div class="sent-request p-4 bg-gray-800 rounded-lg mb-4 flex items-center">
                <!-- Displaying Profile Picture -->
                <img src="https://randomuser.me/api/portraits/men/<?php echo rand(1, 99); ?>.jpg" alt="Profile Pic" class="w-16 h-16 rounded-full mr-4">
                
                <!-- Displaying Name and Email -->
                <div>
                    <p class="text-white font-bold"><?php echo htmlspecialchars($row['name']); ?></p>
                    <p class="text-gray-300"><?php echo htmlspecialchars($row['email']); ?> - <?php echo htmlspecialchars($row['status']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-400">No sent friend requests.</p>
    <?php endif; ?>
</div>

<div class="tab-content block max-h-350px overflow-y-auto" id="friends">
    <h1 class="font-bold text-white">Friends List</h1>
    <?php if (mysqli_num_rows($friendsResult) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($friendsResult)): ?>
            <div class="friend p-4 bg-gray-800 rounded-lg mb-4 flex items-center">
                <!-- Displaying Profile Picture -->
                <img src="https://randomuser.me/api/portraits/men/<?php echo rand(1, 99); ?>.jpg" alt="Profile Pic" class="w-16 h-16 rounded-full mr-4">
                
                <!-- Displaying Name and Email -->
                <div>
                    <p class="text-white font-bold"><?php echo htmlspecialchars($row['name']); ?></p>
                    <p class="text-gray-300"><?php echo htmlspecialchars($row['email']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-gray-400">You have no friends yet.</p>
    <?php endif; ?>
</div>
    </div>
    </div>
  </div>
  <!-- End of modal -->
    </div>
  </div>
  </div>
  <!-- End of main content -->
 
  





   

   <script>
  document.addEventListener('DOMContentLoaded', () => {
  const friendsBtn = document.getElementById('friends-btn');
  const friendsModal = document.getElementById('friends-modal');
  const closeModalBtn = document.querySelector('[data-model="friends-modal"]');
  const tabs = document.querySelectorAll('.tab');
  const tabContents = document.querySelectorAll('.tab-content');
  const searchModal = document.getElementById('search-modal');
  const searchInput = document.querySelector('input[placeholder="Search"]');
  const closeSearchModal = document.querySelector('.close-search-modal');
  const emailSearch = document.getElementById('email-search');
  const searchResults = document.getElementById('search-results');
  const chatInput = document.getElementById('chat-input');
  const sendMessageButton = document.getElementById('send-message');
  const chatMessages = document.getElementById('chat-messages');
  let friendId = null;

  // Utility to add event listeners
  const addEventListenerToElement = (element, event, callback) => {
    if (element) {
      element.addEventListener(event, callback);
    }
  };

  // Open Friends Modal
  addEventListenerToElement(friendsBtn, 'click', () => {
    friendsModal?.classList.remove('hidden');
  });

  // Close Friends Modal
  addEventListenerToElement(closeModalBtn, 'click', () => {
    friendsModal?.classList.add('hidden');
  });

  // Close Modal on outside click
  addEventListenerToElement(friendsModal, 'click', (event) => {
    if (event.target === friendsModal) {
      friendsModal.classList.add('hidden');
    }
  });

  // Tab switching logic
  tabs.forEach((tab) => {
    tab.addEventListener('click', function () {
      const tabId = this.getAttribute('data-tab');

      tabs.forEach((t) => {
        t.classList.remove('active', 'text-white', 'border-b-2', 'border-indigo-600', 'font-semibold');
        t.classList.add('text-gray-400');
      });

      this.classList.add('active', 'text-white', 'border-b-2', 'border-indigo-600', 'font-semibold');
      this.classList.remove('text-gray-400');

      tabContents.forEach((content) => {
        content.classList.add('hidden');
      });

      const activeTabContent = document.getElementById(tabId);
      activeTabContent?.classList.remove('hidden');
    });
  });

  // Open search modal on search field click
  addEventListenerToElement(searchInput, 'click', () => {
    searchModal?.classList.remove('hidden');
    searchModal?.classList.add('flex');
  });

  // Close search modal
  addEventListenerToElement(closeSearchModal, 'click', () => {
    searchModal?.classList.add('hidden');
  });

  // Search functionality with debouncing
  let searchTimeout;
  addEventListenerToElement(emailSearch, 'input', function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      const query = emailSearch.value.trim();
      if (query.length > 0) {
        fetchSearchResults(query);
      } else {
        searchResults.innerHTML = '';
      }
    }, 500);
  });

  function fetchSearchResults(query) {
    fetch(`../backend/search.php?email=${encodeURIComponent(query)}`)
      .then((response) => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then((data) => {
        if (data.status === 'success') {
          displaySearchResults(data.users);
        } else {
          searchResults.innerHTML = `<p class="text-gray-500">${data.message}</p>`;
        }
      })
      .catch((error) => {
        console.error('Error fetching search results:', error);
        searchResults.innerHTML = '<p class="text-red-500">Error fetching search results.</p>';
      });
  }

  function displaySearchResults(users) {
    searchResults.innerHTML = '';

    if (!users || users.length === 0) {
      searchResults.innerHTML = '<p class="text-gray-500">No results found.</p>';
      return;
    }

    users.forEach((user) => {
      const resultItem = document.createElement('div');
      resultItem.className = 'flex items-center p-3 border-b border-[#4a4660] last:border-0';

      const avatar = user.id * 10;

      let actionButton = '';

      if (user.friend_status === null) {
        actionButton = createActionButton('bg-blue-500', 'Add Friend', 'add-friend-btn', user.id);
      } else if (user.friend_status === 'pending') {
        actionButton = createActionButton('bg-yellow-500', 'Pending', '', user.id, true);
      } else if (user.friend_status === 'friends') {
        actionButton = createActionButton('bg-green-500', 'Friends', '', user.id, true);
      }

      resultItem.innerHTML = `
        <div class="h-[42px] w-[42px] shrink-0 rounded-full">
          <img src="https://api.dicebear.com/5.x/initials/svg?seed=${avatar}" alt="Avatar" class="h-full w-full rounded-full object-cover">
        </div>
        <div class="flex-grow ml-3">
          <h3 class="text-white font-semibold">${user.name}</h3>
          <p class="text-gray-400 text-sm">${user.email}</p>
        </div>
        <div>
          ${actionButton}
        </div>
      `;
      searchResults.appendChild(resultItem);
    });

    attachFriendRequestHandlers();
  }

  function createActionButton(bgClass, text, className, userId, disabled = false) {
    return `
      <button class="${bgClass} text-white px-4 py-2 rounded flex items-center gap-2 hover:bg-blue-600 transition duration-300 ${className}" ${disabled ? 'disabled' : ''} data-user-id="${userId}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        ${text}
      </button>
    `;
  }

  function attachFriendRequestHandlers() {
    document.querySelectorAll('.add-friend-btn').forEach((button) => {
      button.addEventListener('click', function () {
        const friendId = this.getAttribute('data-user-id');
        sendFriendRequest(friendId, this);
      });
    });
  }

  function sendFriendRequest(friendId, button) {
    const friendName = button.closest('div').previousElementSibling.querySelector('h3').textContent.trim();

    const formData = new FormData();
    formData.append('action', 'addFriend');
    formData.append('friend_id', friendId);
    formData.append('friend_name', friendName);

    fetch('../backend/search.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'success') {
          button.outerHTML = `<span class="text-yellow-400 text-sm">Request Sent</span>`;
        } else {
          button.outerHTML = `<span class="text-red-500 text-sm">${data.message}</span>`;
        }
      })
      .catch((error) => {
        console.error('Error sending friend request:', error);
      });
  }

  // Chat functionality
  document.querySelectorAll('.chat-list-item').forEach((item) => {
    item.addEventListener('click', function () {
      friendId = this.getAttribute('data-friend-id');
      chatMessages.innerHTML = '';
      fetchMessages();
    });
  });

  function sendMessage() {
    if (!friendId) {
      console.error('No friend selected to send a message.');
      return;
    }

    const message = chatInput.value.trim();
    if (message.length === 0) return;

    fetch('../backend/messages_api.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        action: 'sendMessage',
        user_id: <?php echo $_SESSION['user_id']; ?>,
        friend_id: friendId,
        message: message,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'success') {
          appendMessage(message, true);
          chatInput.value = '';
        } else {
          console.error('Error sending message:', data.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }

  function appendMessage(message, isSender) {
    const messageElement = document.createElement('div');
    messageElement.className = isSender ? 'text-right' : 'text-left';
    messageElement.innerHTML = `
      <div class="p-2 rounded-lg ${isSender ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-700'}">
        <p>${message}</p>
      </div>
    `;
    chatMessages.appendChild(messageElement);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  addEventListenerToElement(sendMessageButton, 'click', sendMessage);

  // Fetching messages for selected friend
  function fetchMessages() {
    if (!friendId) return;

    fetch(`../backend/messages_api.php?friend_id=${friendId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'success') {
          data.messages.forEach((message) => {
            appendMessage(message.content, message.user_id === <?php echo $_SESSION['user_id']; ?>);
          });
        } else {
          console.error('Error fetching messages:', data.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
      });
  }
});
</script>
</body>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/flowbite@1.5.1/dist/flowbite.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.5.1/flowbite.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.5.1/datepicker.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.5.1/datepicker.min.js"></script>

</html>