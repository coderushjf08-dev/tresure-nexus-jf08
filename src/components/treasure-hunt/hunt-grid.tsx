import { CyberButton } from "@/components/ui/cyber-button";
import { Clock, Users, Star, Lock, Play } from "lucide-react";

interface Hunt {
  id: string;
  title: string;
  description: string;
  difficulty: "Easy" | "Medium" | "Hard" | "Extreme";
  duration: string;
  players: number;
  rating: number;
  isPrivate?: boolean;
  creator: string;
  puzzleCount: number;
}

const hunts: Hunt[] = [
  {
    id: "1",
    title: "Cyber Labyrinth",
    description: "Navigate through digital mazes and decode encrypted messages in this mind-bending cyber adventure.",
    difficulty: "Hard",
    duration: "45 min",
    players: 234,
    rating: 4.8,
    creator: "QuantumHunter",
    puzzleCount: 12
  },
  {
    id: "2", 
    title: "Neon Mysteries",
    description: "Uncover secrets hidden in plain sight across a futuristic cityscape filled with holographic clues.",
    difficulty: "Medium",
    duration: "30 min",
    players: 156,
    rating: 4.6,
    creator: "CyberSleuth",
    puzzleCount: 8
  },
  {
    id: "3",
    title: "Matrix Protocol",
    description: "Hack into the mainframe and solve algorithmic puzzles to prevent a digital apocalypse.",
    difficulty: "Extreme",
    duration: "90 min",
    players: 89,
    rating: 4.9,
    creator: "CodeBreaker",
    puzzleCount: 20
  },
  {
    id: "4",
    title: "Quantum Paradox",
    description: "Experience puzzles that exist in multiple states simultaneously in this quantum-themed hunt.",
    difficulty: "Easy",
    duration: "20 min",
    players: 345,
    rating: 4.4,
    creator: "ParticlePhysicist",
    puzzleCount: 6
  }
];

const getDifficultyColor = (difficulty: string) => {
  switch (difficulty) {
    case "Easy": return "text-accent neon-accent";
    case "Medium": return "text-primary neon-primary";
    case "Hard": return "text-secondary neon-secondary";
    case "Extreme": return "text-destructive";
    default: return "text-muted-foreground";
  }
};

export const HuntGrid = () => {
  return (
    <section className="py-20 px-6">
      <div className="max-w-7xl mx-auto">
        {/* Section Header */}
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-orbitron font-bold text-gradient-cyber mb-4">
            Featured Hunts
          </h2>
          <p className="text-xl text-muted-foreground font-rajdhani max-w-2xl mx-auto">
            Choose your challenge and dive into immersive puzzle experiences
          </p>
        </div>
        
        {/* Hunt Cards Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
          {hunts.map((hunt) => (
            <div 
              key={hunt.id} 
              className="glass-card p-8 hover-lift group relative overflow-hidden"
            >
              {/* Hover glow effect */}
              <div className="absolute inset-0 bg-gradient-hologram opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
              
              {/* Hunt Header */}
              <div className="flex items-start justify-between mb-6">
                <div className="flex-1">
                  <h3 className="text-2xl font-orbitron font-bold text-foreground mb-2 group-hover:text-gradient-cyber transition-all duration-300">
                    {hunt.title}
                  </h3>
                  <p className="text-sm text-muted-foreground font-rajdhani">
                    by <span className="text-primary">{hunt.creator}</span>
                  </p>
                </div>
                
                {hunt.isPrivate && (
                  <div className="glass-card px-3 py-1 flex items-center space-x-1">
                    <Lock className="w-3 h-3 text-secondary" />
                    <span className="text-xs text-secondary">Private</span>
                  </div>
                )}
              </div>
              
              {/* Description */}
              <p className="text-muted-foreground mb-6 font-rajdhani leading-relaxed">
                {hunt.description}
              </p>
              
              {/* Hunt Stats */}
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="flex items-center space-x-2">
                  <Clock className="w-4 h-4 text-primary" />
                  <span className="text-sm font-rajdhani">{hunt.duration}</span>
                </div>
                
                <div className="flex items-center space-x-2">
                  <Users className="w-4 h-4 text-secondary" />
                  <span className="text-sm font-rajdhani">{hunt.players} players</span>
                </div>
                
                <div className="flex items-center space-x-2">
                  <Star className="w-4 h-4 text-accent" />
                  <span className="text-sm font-rajdhani">{hunt.rating}/5</span>
                </div>
                
                <div className="flex items-center space-x-2">
                  <div className="w-4 h-4 bg-gradient-cyber rounded-full"></div>
                  <span className="text-sm font-rajdhani">{hunt.puzzleCount} puzzles</span>
                </div>
              </div>
              
              {/* Difficulty Badge */}
              <div className="flex items-center justify-between mb-6">
                <div className={`px-3 py-1 rounded-lg glass-card ${getDifficultyColor(hunt.difficulty)}`}>
                  <span className="text-sm font-orbitron font-semibold">
                    {hunt.difficulty}
                  </span>
                </div>
              </div>
              
              {/* Action Button */}
              <CyberButton 
                variant="hologram" 
                className="w-full group-hover:scale-105 transition-transform duration-300"
                glow
              >
                <Play className="w-4 h-4 mr-2" />
                Enter Hunt
              </CyberButton>
            </div>
          ))}
        </div>
        
        {/* Load More */}
        <div className="text-center mt-12">
          <CyberButton variant="outline" size="lg">
            Discover More Hunts
          </CyberButton>
        </div>
      </div>
    </section>
  );
};