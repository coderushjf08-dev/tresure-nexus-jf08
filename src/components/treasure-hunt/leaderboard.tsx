import { Trophy, Crown, Medal, Zap } from "lucide-react";

interface LeaderboardEntry {
  rank: number;
  username: string;
  score: number;
  huntsCompleted: number;
  avgTime: string;
  level: number;
}

const leaderboardData: LeaderboardEntry[] = [
  { rank: 1, username: "QuantumSolver", score: 15420, huntsCompleted: 87, avgTime: "12:34", level: 25 },
  { rank: 2, username: "CyberNinja", score: 14890, huntsCompleted: 82, avgTime: "13:45", level: 24 },
  { rank: 3, username: "CodeBreaker", score: 14356, huntsCompleted: 79, avgTime: "14:12", level: 23 },
  { rank: 4, username: "MatrixMaster", score: 13987, huntsCompleted: 75, avgTime: "15:23", level: 22 },
  { rank: 5, username: "PuzzlePhantom", score: 13654, huntsCompleted: 73, avgTime: "16:01", level: 21 },
  { rank: 6, username: "ByteHunter", score: 13234, huntsCompleted: 71, avgTime: "16:45", level: 20 },
  { rank: 7, username: "DataSeeker", score: 12876, huntsCompleted: 68, avgTime: "17:12", level: 19 },
  { rank: 8, username: "LogicLord", score: 12543, huntsCompleted: 66, avgTime: "17:38", level: 18 },
];

const getRankIcon = (rank: number) => {
  switch (rank) {
    case 1: return <Crown className="w-6 h-6 text-accent neon-accent" />;
    case 2: return <Trophy className="w-6 h-6 text-primary neon-primary" />;
    case 3: return <Medal className="w-6 h-6 text-secondary neon-secondary" />;
    default: return <span className="text-lg font-orbitron font-bold text-muted-foreground">#{rank}</span>;
  }
};

const getRankGlow = (rank: number) => {
  switch (rank) {
    case 1: return "neon-accent animate-pulse-glow";
    case 2: return "neon-primary";
    case 3: return "neon-secondary";
    default: return "";
  }
};

export const Leaderboard = () => {
  return (
    <section className="py-20 px-6 bg-gradient-to-b from-background to-muted/20">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-orbitron font-bold text-gradient-hologram mb-4">
            Global Leaderboard
          </h2>
          <p className="text-xl text-muted-foreground font-rajdhani max-w-2xl mx-auto">
            The ultimate puzzle masters of the digital realm
          </p>
        </div>
        
        {/* Leaderboard Table */}
        <div className="glass-card overflow-hidden">
          {/* Header */}
          <div className="px-8 py-6 border-b border-card-border/30 bg-gradient-cyber/10">
            <div className="grid grid-cols-6 gap-4 text-sm font-orbitron font-semibold text-muted-foreground">
              <div>Rank</div>
              <div className="col-span-2">Hunter</div>
              <div>Score</div>
              <div>Hunts</div>
              <div>Avg Time</div>
            </div>
          </div>
          
          {/* Leaderboard Entries */}
          <div className="divide-y divide-card-border/20">
            {leaderboardData.map((entry) => (
              <div 
                key={entry.rank}
                className={`px-8 py-6 hover:bg-card/50 transition-all duration-300 group ${getRankGlow(entry.rank)}`}
              >
                <div className="grid grid-cols-6 gap-4 items-center">
                  {/* Rank */}
                  <div className="flex items-center">
                    {getRankIcon(entry.rank)}
                  </div>
                  
                  {/* Username & Level */}
                  <div className="col-span-2">
                    <div className="flex items-center space-x-3">
                      <div className="w-10 h-10 bg-gradient-hologram rounded-lg flex items-center justify-center">
                        <span className="text-sm font-orbitron font-bold text-background">
                          {entry.level}
                        </span>
                      </div>
                      <div>
                        <div className="font-rajdhani font-semibold text-lg text-foreground group-hover:text-gradient-cyber transition-all duration-300">
                          {entry.username}
                        </div>
                        <div className="text-sm text-muted-foreground">
                          Level {entry.level}
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  {/* Score */}
                  <div className="font-orbitron font-bold text-primary">
                    {entry.score.toLocaleString()}
                  </div>
                  
                  {/* Hunts Completed */}
                  <div className="font-rajdhani text-foreground">
                    {entry.huntsCompleted}
                  </div>
                  
                  {/* Average Time */}
                  <div className="font-rajdhani text-accent">
                    {entry.avgTime}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
        
        {/* Weekly Challenge */}
        <div className="mt-12 glass-card p-8 relative overflow-hidden">
          <div className="absolute inset-0 bg-gradient-hologram opacity-5"></div>
          <div className="relative z-10">
            <div className="flex items-center justify-between mb-6">
              <div>
                <h3 className="text-2xl font-orbitron font-bold text-gradient-cyber mb-2">
                  Weekly Challenge
                </h3>
                <p className="text-muted-foreground font-rajdhani">
                  Compete for exclusive rewards and eternal glory
                </p>
              </div>
              <div className="flex items-center space-x-2 text-accent neon-accent">
                <Zap className="w-6 h-6" />
                <span className="text-2xl font-orbitron font-bold">2d 14h</span>
              </div>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div className="text-center">
                <div className="text-3xl font-orbitron font-bold text-primary neon-primary mb-2">
                  1,247
                </div>
                <div className="text-sm text-muted-foreground font-rajdhani">
                  Participants
                </div>
              </div>
              
              <div className="text-center">
                <div className="text-3xl font-orbitron font-bold text-secondary neon-secondary mb-2">
                  50,000
                </div>
                <div className="text-sm text-muted-foreground font-rajdhani">
                  Prize Pool
                </div>
              </div>
              
              <div className="text-center">
                <div className="text-3xl font-orbitron font-bold text-accent neon-accent mb-2">
                  Epic
                </div>
                <div className="text-sm text-muted-foreground font-rajdhani">
                  Difficulty
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};